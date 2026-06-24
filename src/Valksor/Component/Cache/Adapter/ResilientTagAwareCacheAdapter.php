<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Davis Zalitis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Component\Cache\Adapter;

use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RedisException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Service\ResetInterface;

use function microtime;

/**
 * Tag-aware cache pool decorator that keeps the application serving when the Redis backend
 * disappears, instead of letting an uncaught RedisException turn every request into a 500.
 *
 * Long-lived worker runtimes (FrankenPHP, RoadRunner) reuse a persistent Redis socket across
 * requests. When the backend restarts or the socket is dropped, the next command throws
 * `RedisException: "Redis server ... went away"`. This decorator wraps every cache operation:
 *
 *   1. The healthy path runs straight against the inner adapter (zero behavioural change).
 *   2. On RedisException it performs ONE reconnect+retry, covering the common case where the
 *      socket went away but the server is already back (phpredis re-establishes on retry).
 *   3. If the retry also fails, a circuit breaker opens for a short cooldown: operations are
 *      served from an in-process ArrayAdapter (uncached but working) without hammering the dead
 *      backend, and a single warning is logged. After the cooldown one probe is attempted; on
 *      success normal service resumes and the fallback is dropped.
 *
 * The breaker self-heals via the cooldown, so it does not depend on the worker loop resetting
 * services between requests.
 */
final class ResilientTagAwareCacheAdapter implements TagAwareAdapterInterface, TagAwareCacheInterface
{
    private readonly LoggerInterface $logger;

    private bool $open = false;

    private float $retryAt = 0.0;

    private (TagAwareAdapterInterface&TagAwareCacheInterface)|null $fallback = null;

    public function __construct(
        private readonly TagAwareAdapterInterface&TagAwareCacheInterface $inner,
        ?LoggerInterface $logger = null,
        private readonly float $cooldownSeconds = 5.0,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null): mixed
    {
        return $this->execute(
            function (TagAwareAdapterInterface&TagAwareCacheInterface $pool) use ($key, $callback, $beta, &$metadata): mixed {
                return $pool->get($key, $callback, $beta, $metadata);
            },
            'get',
        );
    }

    public function delete(
        string $key,
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->delete($key),
            'delete',
        );
    }

    public function getItem(
        mixed $key,
    ): CacheItem {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): CacheItem => $pool->getItem($key),
            'getItem',
        );
    }

    /**
     * @param array<array-key, string> $keys
     *
     * @return iterable<string, CacheItem>
     */
    public function getItems(
        array $keys = [],
    ): iterable {
        return $this->execute(
            static function (TagAwareAdapterInterface&TagAwareCacheInterface $pool) use ($keys): array {
                $items = [];

                foreach ($pool->getItems($keys) as $itemKey => $item) {
                    $items[$itemKey] = $item;
                }

                return $items;
            },
            'getItems',
        );
    }

    public function hasItem(
        mixed $key,
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->hasItem($key),
            'hasItem',
        );
    }

    public function clear(
        string $prefix = '',
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->clear($prefix),
            'clear',
        );
    }

    public function deleteItem(
        mixed $key,
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->deleteItem($key),
            'deleteItem',
        );
    }

    /**
     * @param array<array-key, string> $keys
     */
    public function deleteItems(
        array $keys,
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->deleteItems($keys),
            'deleteItems',
        );
    }

    public function save(
        CacheItemInterface $item,
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->save($item),
            'save',
        );
    }

    public function saveDeferred(
        CacheItemInterface $item,
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->saveDeferred($item),
            'saveDeferred',
        );
    }

    public function commit(): bool
    {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->commit(),
            'commit',
        );
    }

    /**
     * @param array<array-key, string> $tags
     */
    public function invalidateTags(
        array $tags,
    ): bool {
        return $this->execute(
            static fn (TagAwareAdapterInterface&TagAwareCacheInterface $pool): bool => $pool->invalidateTags($tags),
            'invalidateTags',
        );
    }

    /**
     * Close the circuit breaker and forward the lifecycle reset to the inner adapter.
     */
    public function reset(): void
    {
        $this->close();

        if ($this->inner instanceof ResetInterface) {
            $this->inner->reset();
        }
    }

    /**
     * @template TResult
     *
     * @param callable(TagAwareAdapterInterface&TagAwareCacheInterface): TResult $operation
     *
     * @return TResult
     */
    private function execute(
        callable $operation,
        string $method,
    ): mixed {
        if ($this->open) {
            if (microtime(true) < $this->retryAt) {
                return $operation($this->fallback());
            }

            // Cooldown elapsed: probe the real backend once (half-open state).
            try {
                $result = $operation($this->inner);
                $this->close();

                return $result;
            } catch (RedisException $probeFailure) {
                $this->trip($method, $probeFailure);

                return $operation($this->fallback());
            }
        }

        try {
            return $operation($this->inner);
        } catch (RedisException) {
            // The socket may have gone away while the server is already back: reconnect+retry once.
            try {
                return $operation($this->inner);
            } catch (RedisException $retryFailure) {
                $this->trip($method, $retryFailure);

                return $operation($this->fallback());
            }
        }
    }

    private function trip(
        string $method,
        RedisException $error,
    ): void {
        if (!$this->open) {
            $this->open = true;
            $this->logger->warning('Redis cache unavailable, serving from in-process fallback for this worker.', [
                'method' => $method,
                'cooldown_seconds' => $this->cooldownSeconds,
                'exception' => $error->getMessage(),
            ]);
        }

        $this->retryAt = microtime(true) + $this->cooldownSeconds;
    }

    private function close(): void
    {
        $this->open = false;
        $this->retryAt = 0.0;
        $this->fallback = null;
    }

    private function fallback(): TagAwareAdapterInterface&TagAwareCacheInterface
    {
        return $this->fallback ??= new TagAwareAdapter(new ArrayAdapter(maxItems: 1000));
    }
}
