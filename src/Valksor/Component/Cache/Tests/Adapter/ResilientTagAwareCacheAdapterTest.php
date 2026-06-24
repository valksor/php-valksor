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

namespace Valksor\Component\Cache\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use RedisException;
use Symfony\Component\Cache\CacheItem;
use Valksor\Component\Cache\Adapter\ResilientTagAwareCacheAdapter;
use Valksor\Component\Cache\Tests\Fixtures\TagAwareCachePool;

final class ResilientTagAwareCacheAdapterTest extends TestCase
{
    public function testHealthyGetItemDelegatesToInner(): void
    {
        $item = $this->createMock(CacheItem::class);
        $inner = $this->createMock(TagAwareCachePool::class);
        $inner->method('getItem')->with('k')->willReturn($item);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('warning');

        $adapter = new ResilientTagAwareCacheAdapter($inner, $logger);

        self::assertSame($item, $adapter->getItem('k'));
    }

    public function testHealthyGetDelegatesToInner(): void
    {
        $inner = $this->createMock(TagAwareCachePool::class);
        $inner->method('get')->willReturn('cached-value');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('warning');

        $adapter = new ResilientTagAwareCacheAdapter($inner, $logger);

        self::assertSame(
            'cached-value',
            $adapter->get('k', static fn (CacheItemInterface $item): string => 'computed'),
        );
    }

    public function testHealthyInvalidateTagsDelegatesToInner(): void
    {
        $inner = $this->createMock(TagAwareCachePool::class);
        $inner->method('invalidateTags')->with(['tag'])->willReturn(true);

        $adapter = new ResilientTagAwareCacheAdapter($inner, $this->createMock(LoggerInterface::class));

        self::assertTrue($adapter->invalidateTags(['tag']));
    }

    public function testReconnectRetrySucceedsWithoutDegrading(): void
    {
        $item = $this->createMock(CacheItem::class);
        $calls = 0;
        $inner = $this->createMock(TagAwareCachePool::class);
        $inner->method('getItem')->willReturnCallback(static function (mixed $key) use (&$calls, $item): CacheItem {
            ++$calls;

            if (1 === $calls) {
                throw new RedisException('Redis server redis:6379 went away');
            }

            return $item;
        });

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('warning');

        $adapter = new ResilientTagAwareCacheAdapter($inner, $logger);

        self::assertSame($item, $adapter->getItem('k'));
        self::assertSame(2, $calls);
    }

    public function testFallsBackToInProcessCacheAfterRetryFailure(): void
    {
        $calls = 0;
        $inner = $this->createMock(TagAwareCachePool::class);
        $inner->method('getItem')->willReturnCallback(static function (mixed $key) use (&$calls): CacheItem {
            ++$calls;

            throw new RedisException('Redis server redis:6379 went away');
        });

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $adapter = new ResilientTagAwareCacheAdapter($inner, $logger);

        $first = $adapter->getItem('k');
        self::assertFalse($first->isHit());
        self::assertSame('k', $first->getKey());

        // Breaker is open within the cooldown: the inner adapter must not be hit again.
        $second = $adapter->getItem('k');
        self::assertFalse($second->isHit());
        self::assertSame(2, $calls);
    }

    public function testGetComputesValueViaFallbackWhileDegraded(): void
    {
        $inner = $this->createMock(TagAwareCachePool::class);
        $inner->method('get')->willThrowException(new RedisException('Redis server redis:6379 went away'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $adapter = new ResilientTagAwareCacheAdapter($inner, $logger);

        self::assertSame(
            'computed',
            $adapter->get('key1', static fn (CacheItemInterface $item): string => 'computed'),
        );
    }

    public function testResetReClosesBreaker(): void
    {
        $calls = 0;
        $inner = $this->createMock(TagAwareCachePool::class);
        $inner->method('get')->willReturnCallback(static function (string $key, callable $callback) use (&$calls): mixed {
            ++$calls;

            if ($calls <= 2) {
                throw new RedisException('Redis server redis:6379 went away');
            }

            return 'live-value';
        });

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $adapter = new ResilientTagAwareCacheAdapter($inner, $logger);

        self::assertSame(
            'fallback',
            $adapter->get('a', static fn (CacheItemInterface $item): string => 'fallback'),
        );

        $adapter->reset();

        self::assertSame(
            'live-value',
            $adapter->get('b', static fn (CacheItemInterface $item): string => 'unused'),
        );
        self::assertSame(3, $calls);
    }
}
