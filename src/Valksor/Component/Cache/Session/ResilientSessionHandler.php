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

namespace Valksor\Component\Cache\Session;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RedisException;
use SessionHandlerInterface;
use SessionUpdateTimestampHandlerInterface;

/**
 * Session handler decorator that degrades to a stateless session instead of throwing an uncaught
 * RedisException when the Redis backend is unreachable.
 *
 * A Redis-backed session handler that throws on read/write turns every request into a 500 during a
 * backend outage. Wrapping it lets a transient loss surface as "no persisted session for now" (the
 * user is not kept logged in, but the page still renders) rather than a hard error. The warning is
 * logged once per worker process to avoid log floods during a sustained outage.
 */
final class ResilientSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    private readonly LoggerInterface $logger;

    private bool $warned = false;

    public function __construct(
        private readonly SessionHandlerInterface&SessionUpdateTimestampHandlerInterface $inner,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function open(
        string $path,
        string $name,
    ): bool {
        try {
            return $this->inner->open($path, $name);
        } catch (RedisException $error) {
            $this->degrade('open', $error);

            return true;
        }
    }

    public function close(): bool
    {
        try {
            return $this->inner->close();
        } catch (RedisException $error) {
            $this->degrade('close', $error);

            return true;
        }
    }

    public function read(
        string $id,
    ): string {
        try {
            return $this->inner->read($id);
        } catch (RedisException $error) {
            $this->degrade('read', $error);

            return '';
        }
    }

    public function write(
        string $id,
        string $data,
    ): bool {
        try {
            return $this->inner->write($id, $data);
        } catch (RedisException $error) {
            $this->degrade('write', $error);

            return false;
        }
    }

    public function destroy(
        string $id,
    ): bool {
        try {
            return $this->inner->destroy($id);
        } catch (RedisException $error) {
            $this->degrade('destroy', $error);

            return true;
        }
    }

    public function gc(
        int $max_lifetime,
    ): int|false {
        try {
            return $this->inner->gc($max_lifetime);
        } catch (RedisException $error) {
            $this->degrade('gc', $error);

            return 0;
        }
    }

    public function validateId(
        string $id,
    ): bool {
        try {
            return $this->inner->validateId($id);
        } catch (RedisException $error) {
            $this->degrade('validateId', $error);

            return false;
        }
    }

    public function updateTimestamp(
        string $id,
        string $data,
    ): bool {
        try {
            return $this->inner->updateTimestamp($id, $data);
        } catch (RedisException $error) {
            $this->degrade('updateTimestamp', $error);

            return false;
        }
    }

    private function degrade(
        string $operation,
        RedisException $error,
    ): void {
        if ($this->warned) {
            return;
        }

        $this->warned = true;
        $this->logger->warning('Redis session backend unavailable, degrading to a stateless session.', [
            'operation' => $operation,
            'exception' => $error->getMessage(),
        ]);
    }
}
