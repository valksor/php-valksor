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

namespace {
    use Composer\Json\JsonFile;
    use Valksor\Bundle\Tests\Fixtures\JsonFileStub;

    if (!class_exists(JsonFile::class, autoload: false)) {
        $fixture = __DIR__ . '/Fixtures/JsonFileStub.php';

        if (is_file($fixture)) {
            require $fixture;
            class_alias(JsonFileStub::class, JsonFile::class);
        }
    }
}

namespace Valksor\Bundle\Tests {
    use PHPUnit\Framework\TestCase;
    use ReflectionException;
    use ReflectionMethod;
    use ReflectionProperty;
    use Seld\JsonLint\ParsingException;
    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
    use Symfony\Component\Config\Definition\Loader\DefinitionFileLoader;
    use Symfony\Component\Config\FileLocatorInterface;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Extension\Extension;
    use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
    use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
    use Valksor\Bundle\DependencyInjection\AbstractDependencyConfiguration;
    use Valksor\Bundle\Tests\Fixtures\ExampleComponentConfiguration;
    use Valksor\Bundle\Tests\Fixtures\TrackingDependency;
    use Valksor\Bundle\ValksorBundle;
    use Valksor\Functions\Memoize\MemoizeCache;

    use function is_file;

    final class ExampleDependency extends AbstractDependencyConfiguration
    {
    }

    final class ValksorBundleTest extends TestCase
    {
        private ?string $temporaryAutoloadMap = null;

        /**
         * @throws ParsingException
         */
        public function testBuildInvokesTrackingDependency(): void
        {
            $bundle = new ValksorBundle();
            $this->withDiscoveredComponents($bundle, [
                'tracking_component' => [
                    'class' => TrackingDependency::class,
                    'available' => true,
                ],
            ], function () use ($bundle): void {
                $bundle->build(new ContainerBuilder());
            });

            self::assertSame(1, TrackingDependency::$buildCalls);
        }

        /**
         * @throws ReflectionException
         */
        public function testCallbackRunsWhenComponentEnabled(): void
        {
            $bundle = new ValksorBundle();
            $builder = new ContainerBuilder();
            $builder->setParameter('valksor.example.enabled', true);

            $method = new ReflectionMethod(ValksorBundle::class, 'callback');

            $componentData = [
                'class' => ExampleDependency::class,
                'available' => true,
            ];

            $invocations = 0;
            $method->invoke($bundle, 'example', $componentData, function (object $object, string $class, string $component) use (&$invocations): void {
                $invocations++;
                self::assertInstanceOf(ExampleDependency::class, $object);
                self::assertSame(ExampleDependency::class, $class);
                self::assertSame('example', $component);
            }, $builder);

            self::assertSame(1, $invocations);
        }

        /**
         * @throws ReflectionException
         */
        public function testCallbackSkipsWhenClassIsMissing(): void
        {
            $bundle = new ValksorBundle();
            $builder = new ContainerBuilder();
            $builder->setParameter('valksor.example.enabled', true);

            $method = new ReflectionMethod(ValksorBundle::class, 'callback');

            $componentData = [
                'class' => __CLASS__ . '\\\\MissingComponent',
                'available' => true,
            ];

            $invocations = 0;
            $method->invoke($bundle, 'example', $componentData, function () use (&$invocations): void {
                $invocations++;
            }, $builder);

            self::assertSame(0, $invocations);
        }

        /**
         * @throws ReflectionException
         */
        public function testCallbackSkipsWhenComponentDisabled(): void
        {
            $bundle = new ValksorBundle();
            $builder = new ContainerBuilder();
            $builder->setParameter('valksor.example.enabled', false);

            $method = new ReflectionMethod(ValksorBundle::class, 'callback');

            $componentData = [
                'class' => ExampleDependency::class,
                'available' => true,
            ];

            $invocations = 0;
            $method->invoke($bundle, 'example', $componentData, function () use (&$invocations): void {
                $invocations++;
            }, $builder);

            self::assertSame(0, $invocations);
        }

        /**
         * @throws ReflectionException
         */
        public function testCallbackSkipsWhenComponentUnavailable(): void
        {
            $bundle = new ValksorBundle();
            $builder = new ContainerBuilder();
            $builder->setParameter('valksor.example.enabled', true);

            $method = new ReflectionMethod(ValksorBundle::class, 'callback');

            $componentData = [
                'class' => ExampleDependency::class,
                'available' => false,
            ];

            $invocations = 0;
            $method->invoke($bundle, 'example', $componentData, function () use (&$invocations): void {
                $invocations++;
            }, $builder);

            self::assertSame(0, $invocations);
        }

        /**
         * @throws ReflectionException
         */
        public function testCallbackSkipsWhenEnabledFlagNotBoolean(): void
        {
            $bundle = new ValksorBundle();
            $builder = new ContainerBuilder();
            $builder->setParameter('valksor.example.enabled', 'yes');

            $method = new ReflectionMethod(ValksorBundle::class, 'callback');

            $componentData = [
                'class' => ExampleDependency::class,
                'available' => true,
            ];

            $invocations = 0;
            $method->invoke($bundle, 'example', $componentData, function () use (&$invocations): void {
                $invocations++;
            }, $builder);

            self::assertSame(0, $invocations);
        }

        /**
         * @throws ParsingException
         */
        public function testConfigureInvokesAddSectionOnTrackingDependency(): void
        {
            $bundle = new ValksorBundle();
            $treeBuilder = new TreeBuilder('valksor');
            $loader = new DefinitionFileLoader($treeBuilder, new class implements FileLocatorInterface {
                public function locate(
                    string $name,
                    ?string $currentPath = null,
                    bool $first = true,
                ): string {
                    return $name;
                }
            });
            $definition = new DefinitionConfigurator($treeBuilder, $loader, __FILE__, 'dummy.php');

            $this->withDiscoveredComponents($bundle, [
                'tracking_component' => [
                    'class' => TrackingDependency::class,
                    'available' => true,
                ],
            ], function () use ($bundle, $definition): void {
                $bundle->configure($definition);
            });

            self::assertSame(1, TrackingDependency::$addSectionCalls);
        }

        /**
         * @throws ReflectionException
         */
        public function testDiscoverComponentsReturnsCachedValue(): void
        {
            $bundle = new ValksorBundle();
            $components = [
                'example_component' => [
                    'class' => ExampleComponentConfiguration::class,
                    'available' => true,
                ],
            ];

            $property = new ReflectionProperty(ValksorBundle::class, 'discoveredComponents');
            $property->setValue($bundle, $components);

            $method = new ReflectionMethod(ValksorBundle::class, 'discoverComponents');

            self::assertSame($components, $method->invoke($bundle));
        }

        public function testGetConfigAndParameterHelpers(): void
        {
            $builder = new ContainerBuilder();
            $builder->registerExtension(new class extends Extension {
                public function load(
                    array $configs,
                    ContainerBuilder $container,
                ): void {
                }

                public function getAlias(): string
                {
                    return 'valksor';
                }
            });
            $builder->loadFromExtension('valksor', ['example' => ['enabled' => true]]);
            $builder->loadFromExtension('valksor', ['example' => ['path' => '/tmp']]);

            $config = ValksorBundle::getConfig('valksor', $builder);
            self::assertSame([
                'example' => [
                    'enabled' => true,
                    'path' => '/tmp',
                ],
            ], $config);

            $builder->setParameter('valksor.example.option', 'value');
            self::assertSame('value', ValksorBundle::p($builder, 'example', 'option'));
        }

        public function testLoadExtensionRegistersComponentConfiguration(): void
        {
            $bundle = new ValksorBundle();
            $builder = new ContainerBuilder();
            $this->registerExtension($builder, 'valksor');

            $configurator = $this->createConfigurator($builder);

            $config = [
                'tracking_component' => [
                    'enabled' => true,
                    'option' => 'value',
                ],
            ];

            $this->withDiscoveredComponents($bundle, [
                'tracking_component' => [
                    'class' => TrackingDependency::class,
                    'available' => true,
                ],
            ], function () use ($bundle, $config, $configurator, $builder): void {
                $bundle->loadExtension($config, $configurator, $builder);
            });

            self::assertTrue($builder->hasParameter('valksor.tracking_component.enabled'));
            self::assertTrue($builder->getParameter('valksor.tracking_component.enabled'));
            self::assertSame('value', $builder->getParameter('valksor.tracking_component.option'));
            self::assertSame(1, TrackingDependency::$registerConfigurationCalls);
            self::assertSame('tracking_component', TrackingDependency::$registerConfigurationArgs[0][2]);
        }

        public function testLoadExtensionSkipsComponentWhenDisabled(): void
        {
            $bundle = new ValksorBundle();
            $builder = new ContainerBuilder();
            $this->registerExtension($builder, 'valksor');

            $configurator = $this->createConfigurator($builder);

            $config = [
                'tracking_component' => [
                    'enabled' => false,
                ],
            ];

            $this->withDiscoveredComponents($bundle, [
                'tracking_component' => [
                    'class' => TrackingDependency::class,
                    'available' => true,
                ],
            ], function () use ($bundle, $config, $configurator, $builder): void {
                $bundle->loadExtension($config, $configurator, $builder);
            });

            self::assertTrue($builder->hasParameter('valksor.tracking_component.enabled'));
            self::assertFalse($builder->getParameter('valksor.tracking_component.enabled'));
            self::assertSame(0, TrackingDependency::$registerConfigurationCalls);
        }

        /**
         * @throws ReflectionException
         */
        public function testMemoizeCreatesMemoizeCacheInstances(): void
        {
            $bundle = new ValksorBundle();
            $method = new ReflectionMethod(ValksorBundle::class, 'memoize');

            $first = $method->invoke($bundle);
            $second = $method->invoke($bundle);

            self::assertInstanceOf(MemoizeCache::class, $first);
            self::assertInstanceOf(MemoizeCache::class, $second);
        }

        /**
         * @throws ParsingException
         */
        public function testPrependExtensionRegistersPreConfigurationAndGlobalMigrations(): void
        {
            $bundle = new ValksorBundle();
            TrackingDependency::$usesDoctrine = true;

            $builder = new ContainerBuilder();

            foreach (['framework', 'doctrine', 'doctrine_migrations'] as $alias) {
                $this->registerExtension($builder, $alias);
            }

            $configurator = $this->createConfigurator($builder);

            $this->withDiscoveredComponents($bundle, [
                'tracking_component' => [
                    'class' => TrackingDependency::class,
                    'available' => true,
                ],
            ], function () use ($bundle, $configurator, $builder): void {
                $bundle->prependExtension($configurator, $builder);
            });

            self::assertSame(1, TrackingDependency::$registerPreConfigurationCalls);
            self::assertSame(1, TrackingDependency::$usesDoctrineCalls);

            $frameworkConfig = $builder->getExtensionConfig('framework');
            self::assertNotEmpty($frameworkConfig);
            self::assertTrue($frameworkConfig[0]['set_locale_from_accept_language']);

            $migrationsConfig = $builder->getExtensionConfig('doctrine_migrations');
            self::assertNotEmpty($migrationsConfig);
            self::assertArrayHasKey('Valksor\\Bundle\\Migrations', $migrationsConfig[0]['migrations_paths']);
        }

        /**
         * @throws ParsingException
         */
        public function testPrependExtensionSkipsGlobalMigrationsWhenDoctrineUnused(): void
        {
            $bundle = new ValksorBundle();

            $builder = new ContainerBuilder();

            foreach (['framework', 'doctrine', 'doctrine_migrations'] as $alias) {
                $this->registerExtension($builder, $alias);
            }

            $configurator = $this->createConfigurator($builder);

            $this->withDiscoveredComponents($bundle, [
                'tracking_component' => [
                    'class' => TrackingDependency::class,
                    'available' => true,
                ],
            ], function () use ($bundle, $configurator, $builder): void {
                $bundle->prependExtension($configurator, $builder);
            });

            self::assertSame(1, TrackingDependency::$registerPreConfigurationCalls);
            self::assertSame(1, TrackingDependency::$usesDoctrineCalls);
            self::assertSame([], $builder->getExtensionConfig('doctrine_migrations'));
        }

        protected function tearDown(): void
        {
            TrackingDependency::reset();

            if (null !== $this->temporaryAutoloadMap && is_file($this->temporaryAutoloadMap)) {
                unlink($this->temporaryAutoloadMap);
            }

            $this->temporaryAutoloadMap = null;
        }

        private function createConfigurator(
            ContainerBuilder $builder,
        ): ContainerConfigurator {
            $instanceof = [];
            $locator = new class implements FileLocatorInterface {
                public function locate(
                    string $name,
                    ?string $currentPath = null,
                    bool $first = true,
                ): string {
                    return $name;
                }
            };

            $loader = new class($builder, $locator) extends PhpFileLoader {
                public function __construct(
                    ContainerBuilder $container,
                    FileLocatorInterface $locator,
                ) {
                    parent::__construct($container, $locator);
                }

                public function import(
                    mixed $resource,
                    ?string $type = null,
                    bool|string $ignoreErrors = false,
                    ?string $sourceResource = null,
                    mixed $exclude = null,
                ): mixed {
                    return null;
                }
            };

            return new ContainerConfigurator($builder, $loader, $instanceof, __FILE__, 'bundle_test.php');
        }

        private function registerExtension(
            ContainerBuilder $builder,
            string $alias,
        ): void {
            if ($builder->hasExtension($alias)) {
                return;
            }

            $builder->registerExtension(new class($alias) extends Extension {
                public function __construct(
                    private readonly string $alias,
                ) {
                }

                public function load(
                    array $configs,
                    ContainerBuilder $container,
                ): void {
                }

                public function getAlias(): string
                {
                    return $this->alias;
                }
            });
        }

        private function withDiscoveredComponents(
            ValksorBundle $bundle,
            array $components,
            callable $callback,
        ): void {
            $property = new ReflectionProperty(ValksorBundle::class, 'discoveredComponents');

            $original = $property->getValue($bundle);
            $property->setValue($bundle, $components);

            try {
                $callback();
            } finally {
                $property->setValue($bundle, $original);
            }
        }
    }
}
