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

namespace Valksor\Component\Cache\Tests\Session;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RedisException;
use Valksor\Component\Cache\Session\ResilientSessionHandler;
use Valksor\Component\Cache\Tests\Fixtures\SessionHandler;

final class ResilientSessionHandlerTest extends TestCase
{
    public function testHealthyReadDelegatesToInner(): void
    {
        $inner = $this->createMock(SessionHandler::class);
        $inner->method('read')->with('sid')->willReturn('payload');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('warning');

        $handler = new ResilientSessionHandler($inner, $logger);

        self::assertSame('payload', $handler->read('sid'));
    }

    public function testReadReturnsEmptyAndWarnsOnRedisException(): void
    {
        $inner = $this->createMock(SessionHandler::class);
        $inner->method('read')->willThrowException(new RedisException('Redis server redis:6379 went away'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $handler = new ResilientSessionHandler($inner, $logger);

        self::assertSame('', $handler->read('sid'));
    }

    public function testWriteReturnsFalseOnRedisException(): void
    {
        $inner = $this->createMock(SessionHandler::class);
        $inner->method('write')->willThrowException(new RedisException('Redis server redis:6379 went away'));

        $handler = new ResilientSessionHandler($inner);

        self::assertFalse($handler->write('sid', 'data'));
    }

    public function testRemainingOperationsReturnSafeDefaultsOnFailure(): void
    {
        $inner = $this->createMock(SessionHandler::class);
        $inner->method('open')->willThrowException(new RedisException('x'));
        $inner->method('close')->willThrowException(new RedisException('x'));
        $inner->method('destroy')->willThrowException(new RedisException('x'));
        $inner->method('gc')->willThrowException(new RedisException('x'));
        $inner->method('validateId')->willThrowException(new RedisException('x'));
        $inner->method('updateTimestamp')->willThrowException(new RedisException('x'));

        $handler = new ResilientSessionHandler($inner);

        self::assertTrue($handler->open('/path', 'name'));
        self::assertTrue($handler->close());
        self::assertTrue($handler->destroy('sid'));
        self::assertSame(0, $handler->gc(3600));
        self::assertFalse($handler->validateId('sid'));
        self::assertFalse($handler->updateTimestamp('sid', 'data'));
    }

    public function testWarnsOnlyOncePerInstance(): void
    {
        $inner = $this->createMock(SessionHandler::class);
        $inner->method('read')->willThrowException(new RedisException('x'));
        $inner->method('write')->willThrowException(new RedisException('x'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $handler = new ResilientSessionHandler($inner, $logger);

        $handler->read('sid');
        $handler->write('sid', 'data');
    }
}
