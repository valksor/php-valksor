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

namespace Valksor\Component\Cache\Redis;

use Redis;
use RedisException;

/**
 * Service configurator that applies TCP keepalive and a read timeout to a phpredis client.
 *
 * The SncRedisBundle phpredis factory connects with `pconnect()` but never sets
 * `OPT_TCP_KEEPALIVE`, so a silently dropped socket (backend restart, overlay reconfiguration) is
 * only discovered when the next command fails. Enabling keepalive lets the OS reap dead peers, and
 * a bounded read timeout makes a half-open socket fail fast instead of blocking the worker.
 *
 * Attached to each `snc_redis.*` client via a compiler pass so the vendored bundle is left
 * untouched. setOption is best-effort: a not-yet-connected lazy client may throw, which is harmless
 * here because the cache and session decorators already cover connection failures.
 */
final readonly class RedisClientConfigurator
{
    public function __construct(
        private int $tcpKeepalive = 60,
        private float $readTimeout = 3.0,
    ) {
    }

    public function __invoke(
        Redis $client,
    ): Redis {
        try {
            $client->setOption(Redis::OPT_TCP_KEEPALIVE, $this->tcpKeepalive);
            $client->setOption(Redis::OPT_READ_TIMEOUT, $this->readTimeout);
        } catch (RedisException) {
            // A lazy client that is not connected yet can reject setOption; the resilient
            // decorators handle the connection itself, so this is intentionally ignored.
        }

        return $client;
    }
}
