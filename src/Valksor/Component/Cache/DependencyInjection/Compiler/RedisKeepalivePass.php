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

namespace Valksor\Component\Cache\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Valksor\Component\Cache\Redis\RedisClientConfigurator;

/**
 * Attaches the RedisClientConfigurator to the SncRedisBundle phpredis clients.
 *
 * The configurator is set as a service configurator on each existing client definition rather than
 * by redefining the service ids, so the bundle's own factory and arguments are preserved. The
 * configurator runs after the client is instantiated and applies TCP keepalive / read timeout.
 */
final class RedisKeepalivePass implements CompilerPassInterface
{
    /** @var list<string> */
    private const array CLIENT_SERVICE_IDS = [
        'snc_redis.default',
        'snc_redis.cache',
        'snc_redis.ttl',
    ];

    public function process(
        ContainerBuilder $container,
    ): void {
        if (!$container->hasDefinition(RedisClientConfigurator::class)) {
            return;
        }

        foreach (self::CLIENT_SERVICE_IDS as $serviceId) {
            if (!$container->hasDefinition($serviceId)) {
                continue;
            }

            $container->getDefinition($serviceId)
                ->setConfigurator([new Reference(RedisClientConfigurator::class), '__invoke']);
        }
    }
}
