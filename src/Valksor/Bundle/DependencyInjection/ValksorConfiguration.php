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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class ValksorConfiguration extends AbstractDependencyConfiguration
{
    public function addSection(
        ArrayNodeDefinition $rootNode,
        callable $enableIfStandalone,
        string $component,
    ): void {
        $rootNode
            ->children()
                ->arrayNode('project')
                ->info('Project directory structure configuration')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('apps_dir')
                        ->info('Directory containing applications (relative to project root)')
                        ->defaultNull()
                        ->example('apps')
                    ->end()
                    ->scalarNode('infrastructure_dir')
                        ->info('Directory containing shared resources (relative to project root)')
                        ->defaultNull()
                        ->example('infrastructure')
                    ->end()
                    ->arrayNode('autoload')
                        ->info('Autoload configuration')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('namespace_prefix')
                                ->info('Namespace prefix for generated autoload classes')
                                ->defaultNull()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function registerGlobalMigrations(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        if ($builder->hasExtension('doctrine')) {
            if ($builder->hasExtension('doctrine_migrations')) {
                $container->extension('doctrine_migrations', [
                    'migrations_paths' => [
                        'Valksor\\Bundle\\Migrations' => __DIR__ . '/../Resources/migrations',
                    ],
                ]);
            }
        }
    }

    public function registerPreConfiguration(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
        string $component,
    ): void {
        if ($builder->hasExtension('framework')) {
            $container->extension('framework', [
                'set_locale_from_accept_language' => true,
            ]);
        }
    }
}
