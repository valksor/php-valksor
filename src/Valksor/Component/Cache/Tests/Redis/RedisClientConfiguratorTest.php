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

namespace Valksor\Component\Cache\Tests\Redis;

use PHPUnit\Framework\TestCase;
use Redis;
use RedisException;
use Valksor\Component\Cache\Redis\RedisClientConfigurator;

final class RedisClientConfiguratorTest extends TestCase
{
    public function testAppliesKeepaliveAndReadTimeout(): void
    {
        /** @var array<int, mixed> $captured */
        $captured = [];
        $client = $this->createMock(Redis::class);
        $client->method('setOption')->willReturnCallback(static function (int $option, mixed $value) use (&$captured): bool {
            $captured[$option] = $value;

            return true;
        });

        $result = new RedisClientConfigurator(60, 3.0)($client);

        self::assertSame($client, $result);
        self::assertSame(60, $captured[Redis::OPT_TCP_KEEPALIVE]);
        self::assertSame(3.0, $captured[Redis::OPT_READ_TIMEOUT]);
    }

    public function testSwallowsRedisExceptionFromSetOption(): void
    {
        $client = $this->createMock(Redis::class);
        $client->method('setOption')->willThrowException(new RedisException('Redis server redis:6379 went away'));

        $result = new RedisClientConfigurator()($client);

        self::assertSame($client, $result);
    }
}
