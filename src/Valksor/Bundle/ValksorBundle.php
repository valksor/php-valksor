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
use Psr\Cache\InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use RuntimeException;
use Seld\JsonLint\ParsingException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Contracts\Cache\CacheInterface;
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
use function count;
use function dirname;
use function file_get_contents;
use function implode;
use function in_array;
use function is_a;
use function is_array;
use function is_bool;
use function is_dir;
use function is_file;
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

use const DIRECTORY_SEPARATOR;

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
            $this->callback($component, $componentData, static function (object $object) use ($container): void {
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

        // Track which parent nodes need addDefaultsIfNotSet()
        $parentNodeNeedsDefaults = [];
        $allComponents = $this->discoverComponents();

        // First pass: collect requirements from all components
        foreach ($allComponents as $component => $componentData) {
            $this->callback($component, $componentData, function (object $object, string $class, string $component) use (&$parentNodeNeedsDefaults): void {
                $configPath = $this->getComponentConfigPath($class, $component);

                // Track all parent nodes this component touches
                for ($i = 0; $i < count($configPath) - 1; $i++) {
                    $pathPart = $configPath[$i];

                    if (!isset($parentNodeNeedsDefaults[$pathPart])) {
                        $parentNodeNeedsDefaults[$pathPart] = !$object->usesArrayPrototype();
                    } else {
                        // If ANY component needs defaults, mark it as needed
                        $parentNodeNeedsDefaults[$pathPart] = $parentNodeNeedsDefaults[$pathPart] || !$object->usesArrayPrototype();
                    }
                }
            });
        }

        $createdNodes = [];

        // Second pass: build the configuration tree with collected requirements
        foreach ($allComponents as $component => $componentData) {
            $this->callback($component, $componentData, function (object $object, string $class, string $component) use ($enableIfStandalone, $rootNode, &$createdNodes, $parentNodeNeedsDefaults): void {
                // Get namespace-based path
                $configPath = $this->getComponentConfigPath($class, $component);

                // Build nested structure
                $currentNode = $rootNode;

                // Navigate/create all parent nodes
                for ($i = 0; $i < count($configPath) - 1; $i++) {
                    $pathPart = $configPath[$i];

                    if (!isset($createdNodes[$pathPart])) {
                        $node = $currentNode->children()
                            ->arrayNode($pathPart);

                        // Apply addDefaultsIfNotSet() if any component needs it
                        if ($parentNodeNeedsDefaults[$pathPart] ?? false) {
                            $node->addDefaultsIfNotSet();
                        }

                        $currentNode = $node;
                        $createdNodes[$pathPart] = $currentNode;
                    } else {
                        $currentNode = $createdNodes[$pathPart];
                    }
                }

                // Add component at the final location
                $wrapper = static fn (string $package, string $componentClass) => $enableIfStandalone($package, $class);
                $object->addSection($currentNode, $wrapper, end($configPath));
            });
        }
    }

    /**
     * Get all configuration defaults from discovered components.
     *
     * Uses auto-discovery to collect defaults from all configuration classes
     * into a hierarchical array matching the configuration tree structure.
     *
     * @param CacheInterface|null $cache Optional cache pool for performance.
     *                                   Should be configured by the application using this bundle.
     *
     * @return array<string, mixed> Hierarchical array of all configuration defaults
     *
     * @throws InvalidArgumentException
     * @throws ParsingException
     */
    public function getAllConfigurationDefaults(
        ?CacheInterface $cache = null,
    ): array {
        if (null === $cache) {
            return $this->computeAllDefaults();
        }

        return $cache->get('valksor.bundle.defaults', fn () => $this->computeAllDefaults());
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
            $this->callback($component, $componentData, static function (object $object, string $class, string $component) use ($container, $builder): void {
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
            $this->callback($component, $componentData, static function (object $object, string $class, string $component) use ($container, $builder, &$usesDoctrine): void {
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

        $willBeAvailable = $_helper->willBeAvailable($package, $class, [sprintf('%s/bundle', self::VALKSOR)]);

        if (!$willBeAvailable) {
            return;
        }

        $object = new $class();

        if (is_a($object, Dependency::class)) {
            // Mirror the config-tree default: canBeDisabled() (default true) only when the
            // component is standalone, otherwise canBeEnabled() (default false). $willBeAvailable
            // is already guaranteed true by the early return above.
            $default = !class_exists(FullStack::class);

            if (null !== $builder && !$this->resolveEnabled($builder, $class, $component, $default)) {
                return;
            }

            $callback($object, $class, $component);
        }
    }

    /**
     * Compute all configuration defaults from discovered components.
     *
     * @return array<string, mixed> Hierarchical array of all configuration defaults
     *
     * @throws ParsingException
     */
    private function computeAllDefaults(): array
    {
        $defaults = [];

        foreach ($this->discoverComponents() as $componentId => $componentData) {
            $className = $componentData['class'];

            $componentDefaults = $className::getDefaults();

            if ([] === $componentDefaults) {
                continue;
            }

            $current = &$defaults;

            foreach ($this->getComponentConfigPath($className, $componentId) as $key) {
                if (!isset($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
            $current = $componentDefaults;
        }

        return $defaults;
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

    /**
     * Extract configuration path from component namespace.
     *
     * Maps namespace structure to configuration path, e.g.:
     * - Valksor\Component\FormType\CloudflareTurnstile\DependencyInjection\CloudflareTurnstileConfiguration
     *   → ['form_type', 'cloudflare_turnstile']
     * - Valksor\Component\Sse\DependencyInjection\SseConfiguration
     *   → ['sse']
     *
     * @return list<string>
     */
    private function getComponentConfigPath(
        string $className,
        string $componentName,
    ): array {
        // Handle Valksor components: Valksor\Component\<Category>\...\<Component>Configuration
        $valksorPattern = '#^Valksor\\\\Component\\\\([^\\\\]+)\\\\[^\\\\]+\\\\DependencyInjection\\\\[^\\\\]+$#';

        if (preg_match($valksorPattern, $className, $matches)) {
            $category = $matches[1]; // e.g., "FormType"

            // Convert PascalCase to snake_case
            $configSection = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $category));

            return [$configSection, $componentName];
        }

        // Default: flat structure
        return [$componentName];

        // Default: flat structure
    }

    private function memoize(): MemoizeCache
    {
        return $this->cache ??= new MemoizeCache();
    }

    /**
     * Resolve whether a component is enabled, symmetrically across the prepend and load phases.
     *
     * During loadExtension the flattened `valksor.<component>.enabled` parameter exists and is the
     * authoritative source. During prependExtension that parameter does not exist yet, so the flag
     * is read from the merged extension config using the same normalization Symfony applies to a
     * canBeEnabled()/canBeDisabled() node — falling back to $default when the component is absent.
     */
    private function resolveEnabled(
        ContainerBuilder $builder,
        string $class,
        string $component,
        bool $default,
    ): bool {
        try {
            $path = $this->getComponentConfigPath($class, $component);
            $enabled = $builder->getParameter(sprintf('%s.%s.enabled', self::VALKSOR, implode('.', $path)));

            return is_bool($enabled) && $enabled;
        } catch (ParameterNotFoundException) {
        }

        $config = self::getConfig(self::VALKSOR, $builder);

        foreach ($this->getComponentConfigPath($class, $component) as $key) {
            if (!is_array($config) || !array_key_exists($key, $config)) {
                return $default;
            }

            $config = $config[$key];
        }

        if (null === $config) {
            return true;
        }

        if (is_bool($config)) {
            return $config;
        }

        if (is_array($config)) {
            return (bool) ($config['enabled'] ?? true);
        }

        return $default;
    }
}
