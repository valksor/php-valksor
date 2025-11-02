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

namespace Valksor\Bundle\Tests\Fixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Valksor\Bundle\DependencyInjection\AbstractDependencyConfiguration;

final class TrackingDependency extends AbstractDependencyConfiguration
{
    public static int $addSectionCalls = 0;
    public static int $buildCalls = 0;
    public static array $registerConfigurationArgs = [];
    public static int $registerConfigurationCalls = 0;
    public static array $registerPreConfigurationArgs = [];
    public static int $registerPreConfigurationCalls = 0;
    public static bool $usesDoctrine = false;
    public static int $usesDoctrineCalls = 0;

    public function addSection(
        ArrayNodeDefinition $rootNode,
        callable $enableIfStandalone,
        string $component,
    ): void {
        self::$addSectionCalls++;
        parent::addSection($rootNode, $enableIfStandalone, $component);
    }

    public function build(
        ContainerBuilder $container,
    ): void {
        self::$buildCalls++;
    }

    public function registerConfiguration(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
        string $component,
    ): void {
        self::$registerConfigurationCalls++;
        self::$registerConfigurationArgs[] = [$container, $builder, $component];
    }

    public function registerPreConfiguration(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
        string $component,
    ): void {
        self::$registerPreConfigurationCalls++;
        self::$registerPreConfigurationArgs[] = [$container, $builder, $component];
        parent::registerPreConfiguration($container, $builder, $component);
    }

    public function usesDoctrine(): bool
    {
        self::$usesDoctrineCalls++;

        return self::$usesDoctrine;
    }

    public static function reset(): void
    {
        self::$addSectionCalls = 0;
        self::$registerConfigurationCalls = 0;
        self::$registerConfigurationArgs = [];
        self::$registerPreConfigurationCalls = 0;
        self::$registerPreConfigurationArgs = [];
        self::$buildCalls = 0;
        self::$usesDoctrineCalls = 0;
        self::$usesDoctrine = false;
    }
}
