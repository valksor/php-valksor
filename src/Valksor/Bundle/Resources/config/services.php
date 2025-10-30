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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Valksor\Functions\Memoize\MemoizeCache;

return static function (
    ContainerConfigurator $container,
): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load(namespace: 'Valksor\\Bundle\\', resource: __DIR__ . '/../../*')
        ->exclude(excludes: [__DIR__ . '/../../{Entity,Resources,recipe}']);

    $services->set(MemoizeCache::class);
};
