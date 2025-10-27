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

namespace Valksor\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

#[AutoconfigureTag]
interface Dependency
{
    public function addSection(
        ArrayNodeDefinition $rootNode,
        callable $enableIfStandalone,
        string $component,
    ): void;

    public function autoDiscover(): bool;

    public function build(
        ContainerBuilder $container,
    ): void;

    public function registerConfiguration(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
        string $component,
    ): void;

    public function registerPreConfiguration(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
        string $component,
    ): void;

    public function usesDoctrine(): bool;
}
