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
use Valksor\Bundle\ValksorBundle;
use Valksor\Functions\Php;

use function array_merge_recursive;
use function dirname;
use function is_file;
use function sprintf;

abstract class AbstractDependencyConfiguration implements Dependency
{
    public function addSection(
        ArrayNodeDefinition $rootNode,
        callable $enableIfStandalone,
        string $component,
    ): void {
        $rootNode
            ->children()
                ->arrayNode($component)
                    ->{$enableIfStandalone(sprintf('%s/%s', ValksorBundle::VALKSOR, $component), self::class)}()
                ->end();
    }

    public function autoDiscover(): bool
    {
        return true;
    }

    public function build(
        ContainerBuilder $container,
    ): void {
    }

    public function registerConfiguration(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
        string $component,
    ): void {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Php\Traits\_GetReflection;
            };
        }

        $path = dirname($_helper->getReflection(static::class)->getFileName(), 2) . '/Resources/config/services.php';

        if (is_file($path)) {
            $container->import($path);
        }
    }

    public function registerPreConfiguration(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
        string $component,
    ): void {
    }

    public function usesArrayPrototype(): bool
    {
        return false;
    }

    public function usesDoctrine(): bool
    {
        return false;
    }

    /**
     * Get default configuration values for this component.
     *
     * Override this method in child classes to provide default values.
     * The returned array structure should match the configuration tree
     * defined in addSection().
     *
     * @return array<string, mixed> Default configuration values
     */
    public static function getDefaults(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mergeConfig(
        ContainerBuilder $builder,
        string $extension,
    ): array {
        return array_merge_recursive(...$builder->getExtensionConfig($extension));
    }
}
