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

namespace Valksor\Bundle;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use RuntimeException;
use Seld\JsonLint\ParsingException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Throwable;
use Valksor\Bundle\DependencyInjection\Dependency;
use Valksor\Bundle\DependencyInjection\ValksorConfiguration;
use Valksor\FullStack;
use Valksor\Functions\Iteration;
use Valksor\Functions\Local;
use Valksor\Functions\Memoize\MemoizeCache;

use function array_key_exists;
use function array_merge_recursive;
use function class_exists;
use function dirname;
use function in_array;
use function is_a;
use function is_bool;
use function is_dir;
use function ksort;
use function preg_replace;
use function rtrim;
use function sprintf;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strlen;
use function strtolower;
use function substr;

final class ValksorBundle extends AbstractBundle
{
    public const string VALKSOR = 'valksor';

    private const array SELFS = [
        'valksor',
        'valksor-dev',
        'valksor-plugin',
    ];

    private ?MemoizeCache $cache = null;

    /** @var array<string, array{class: string, available: bool}>|null */
    private ?array $discoveredComponents = null;

    private ?string $projectDir = null;

    public function boot(): void
    {
        parent::boot();
        $this->memoize();
    }

    /**
     * @throws ParsingException
     */
    public function build(
        ContainerBuilder $container,
    ): void {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Local\Traits\_Exists;
                use Local\Traits\_WillBeAvailable;
            };
        }

        if (null === $this->projectDir) {
            $bag = $container->getParameterBag();

            if ($bag->has('kernel.project_dir')) {
                $this->projectDir = $bag->get('kernel.project_dir');
            }
        }

        foreach ($this->discoverComponents() as $component => $componentData) {
            $this->callback($component, $componentData, function (object $object) use ($container): void {
                $object->build($container);
            });
        }

        new ValksorConfiguration()->build($container);
    }

    /**
     * @throws ParsingException
     */
    public function configure(
        DefinitionConfigurator $definition,
    ): void {
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $definition
            ->rootNode();

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Local\Traits\_Exists;
                use Local\Traits\_WillBeAvailable;
            };
        }

        $willBeAvailable = static function (string $package, string $class, ?string $parentPackage = null) use ($_helper) {
            $parentPackages = (array) $parentPackage;
            $parentPackages[] = sprintf('%s/bundle', self::VALKSOR);

            return $_helper->willBeAvailable($package, $class, $parentPackages);
        };

        $enableIfStandalone = static fn (string $package, string $class) => !class_exists(FullStack::class) && $willBeAvailable($package, $class) ? 'canBeDisabled' : 'canBeEnabled';

        $wrapper = static fn (string $package, string $componentClass) => $enableIfStandalone($package, '');
        new ValksorConfiguration()->addSection($rootNode, $wrapper, '');

        foreach ($this->discoverComponents() as $component => $componentData) {
            $this->callback($component, $componentData, function (object $object, string $class, string $component) use ($enableIfStandalone, $rootNode): void {
                $wrapper = static fn (string $package, string $componentClass) => $enableIfStandalone($package, $class);
                $object->addSection($rootNode, $wrapper, $component);
            });
        }
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws ParsingException
     */
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Iteration\Traits\_MakeOneDimension;
                use Local\Traits\_Exists;
                use Local\Traits\_WillBeAvailable;
            };
        }

        foreach ($_helper->makeOneDimension([self::VALKSOR => $config]) as $key => $value) {
            $builder->setParameter($key, $value);
        }

        foreach ($this->discoverComponents() as $component => $componentData) {
            $this->callback($component, $componentData, function (object $object, string $class, string $component) use ($container, $builder): void {
                $object->registerConfiguration($container, $builder, $component);
            }, $builder);
        }

        new ValksorConfiguration()->registerConfiguration($container, $builder, '');
    }

    /**
     * @throws ParsingException
     */
    public function prependExtension(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $valksor = new ValksorConfiguration();

        $usesDoctrine = false;

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Local\Traits\_Exists;
                use Local\Traits\_WillBeAvailable;
            };
        }

        if (null === $this->projectDir) {
            $bag = $builder->getParameterBag();

            if ($bag->has('kernel.project_dir')) {
                $this->projectDir = $bag->get('kernel.project_dir');
            }
        }

        foreach ($this->discoverComponents() as $component => $componentData) {
            $this->callback($component, $componentData, function (object $object, string $class, string $component) use ($container, $builder, &$usesDoctrine): void {
                $object->registerPreConfiguration($container, $builder, $component);
                $usesDoctrine = $usesDoctrine || $object->usesDoctrine();
            }, $builder);
        }

        $valksor->registerPreConfiguration($container, $builder, '');

        if ($usesDoctrine) {
            $valksor->registerGlobalMigrations($container, $builder);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getConfig(
        string $package,
        ContainerBuilder $builder,
    ): array {
        return array_merge_recursive(...$builder->getExtensionConfig($package));
    }

    public static function p(
        ContainerBuilder $builder,
        string $component,
        string $parameter,
    ): mixed {
        return $builder->getParameter(sprintf('%s.%s.%s', self::VALKSOR, $component, $parameter));
    }

    /**
     * @param array<string, mixed> $componentData
     */
    private function callback(
        string $component,
        array $componentData,
        callable $callback,
        ?ContainerBuilder $builder = null,
    ): void {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Local\Traits\_Exists;
                use Local\Traits\_WillBeAvailable;
            };
        }

        $class = $componentData['class'];

        if (!$_helper->exists($class)) {
            return;
        }

        if (!$componentData['available']) {
            return;
        }

        $package = self::VALKSOR . '/' . $component;

        if (!$_helper->willBeAvailable($package, $class, [sprintf('%s/bundle', self::VALKSOR)])) {
            return;
        }

        $object = new $class();

        if (is_a($object, Dependency::class)) {
            if (null !== $builder) {
                try {
                    $enabled = self::p($builder, $component, 'enabled');

                    if (!is_bool($enabled) || !$enabled) {
                        return;
                    }
                } catch (Throwable) {
                }
            }

            $callback($object, $class, $component);
        }
    }

    /**
     * @return array<string, array{class: string, available: bool}> Array of component ID => {class, available}
     *
     * @throws ParsingException
     */
    private function discoverComponents(): array
    {
        if (null !== $this->discoveredComponents) {
            return $this->discoveredComponents;
        }

        $this->discoveredComponents = [];
        $visitedClasses = [];

        $autoloadPsr4 = require $this->findProjectRoot() . '/vendor/composer/autoload_psr4.php';

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Traits\_LoadReflection;
            };
        }

        foreach ($autoloadPsr4 as $namespacePrefix => $directories) {
            if (!str_starts_with($namespacePrefix, 'Valksor\\') && !str_starts_with($namespacePrefix, 'ValksorDev\\')) {
                continue;
            }

            foreach ($directories as $directory) {
                foreach ($this->findConfigurationClasses($directory, $namespacePrefix) as $className) {
                    if (array_key_exists($className, $visitedClasses)) {
                        continue;
                    }

                    $visitedClasses[$className] = true;

                    try {
                        $reflection = $_helper->loadReflection($className, $this->memoize());
                    } catch (ReflectionException) {
                        continue;
                    }

                    if (!$reflection->implementsInterface(Dependency::class) || $reflection->isAbstract()) {
                        continue;
                    }

                    $componentName = substr($reflection->getShortName(), 0, -13);
                    $componentId = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $componentName));

                    if (self::VALKSOR === $componentId || isset($this->discoveredComponents[$componentId])) {
                        continue;
                    }

                    try {
                        $available = new $className()->autoDiscover();
                    } catch (Throwable) {
                        $available = false;
                    }

                    $this->discoveredComponents[$componentId] = [
                        'class' => $className,
                        'available' => $available,
                    ];
                }
            }
        }

        ksort($this->discoveredComponents);

        return $this->discoveredComponents;
    }

    /**
     * @return iterable<string>
     */
    private function findConfigurationClasses(
        string $directory,
        string $namespacePrefix,
    ): iterable {
        $normalizedDirectory = rtrim($directory, DIRECTORY_SEPARATOR . '/');

        if ('' === $normalizedDirectory || !is_dir($normalizedDirectory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($normalizedDirectory, FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || 'php' !== $file->getExtension()) {
                continue;
            }

            $basename = $file->getBasename('.php');

            if (!str_ends_with($basename, 'Configuration')) {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen($normalizedDirectory) + 1);
            $relativeClass = substr($relativePath, 0, -4);
            $relativeClass = str_replace(DIRECTORY_SEPARATOR, '\\', $relativeClass);

            yield rtrim($namespacePrefix, '\\') . '\\' . $relativeClass;
        }
    }

    /**
     * Recursively find the project root by looking for composer.json.
     *
     * @throws ParsingException
     */
    private function findProjectRoot(): string
    {
        if (null !== $this->projectDir) {
            return $this->projectDir;
        }

        $dir = __DIR__;

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Iteration\Traits\_JsonDecode;
            };
        }

        while ($dir !== dirname($dir)) {
            // Check if this is the actual project root (has vendor directory)
            if (is_file($dir . '/composer.json')) {
                $data = $_helper->jsonDecode(file_get_contents($dir . '/composer.json'), true);

                if (is_dir($dir . '/vendor') && !in_array($data['name'], self::SELFS, true)) {
                    return $this->projectDir = $dir;
                }
            }
            $dir = dirname($dir);
        }

        throw new RuntimeException('Could not find project root (composer.json with vendor directory)');
    }

    private function memoize(): MemoizeCache
    {
        return $this->cache ??= new MemoizeCache();
    }
}
