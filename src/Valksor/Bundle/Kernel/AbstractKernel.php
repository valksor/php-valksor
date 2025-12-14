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

namespace Valksor\Bundle\Kernel;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use function array_merge;
use function explode;
use function file_exists;
use function glob;
use function is_dir;
use function is_file;
use function sort;
use function sprintf;
use function strlen;
use function ucfirst;

use const GLOB_NOSORT;

abstract class AbstractKernel extends BaseKernel
{
    use MicroKernelTrait;
    protected ?string $apps = null;

    protected ?string $infrastructure = null;

    /** @var array<string, BundleInterface>|null */
    private ?array $allBundles = null;

    public function __construct(
        string $environment,
        bool $debug,
        private readonly string $id,
    ) {
        $_SERVER['APP_KERNEL_NAME'] = $this->id;
        $_ENV['APP_KERNEL_NAME'] = $this->id;

        if (null === $this->apps) {
            throw new LogicException('Apps dir not set');
        }

        if (null === $this->infrastructure) {
            throw new LogicException('Infrastructure dir not set');
        }

        parent::__construct($environment, $debug);

        $this->loadAppEnvironmentFiles();
    }

    public function getAppConfigDir(): string
    {
        return $this->getProjectDir() . '/' . $this->apps . '/' . $this->id . '/config';
    }

    public function getCacheDir(): string
    {
        return (($_SERVER['APP_CACHE_DIR'] ?? $this->getProjectDir()) . '/var/cache') . '/' . $this->id . '/' . $this->environment;
    }

    public function getConfigDir(): string
    {
        return $this->getProjectDir() . '/' . $this->infrastructure . '/config';
    }

    public function getLogDir(): string
    {
        return (($_SERVER['APP_LOG_DIR'] ?? $this->getProjectDir()) . '/var/log') . '/' . $this->id;
    }

    public function registerBundles(): iterable
    {
        foreach ($this->getAllBundles() as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(
        ContainerConfigurator $container,
    ): void {
        $infrastructureDir = $this->getProjectDir() . '/' . $this->infrastructure . '/config';
        $appDir = $this->getAppConfigDir();

        $container->parameters()
            ->set('app.id', $this->id)
            ->set('app.namespace', ucfirst(explode('.', $this->id, 2)[0]));

        $this->importInfrastructurePackagesWithOverride($infrastructureDir, $appDir, $container);
        $this->importConfig($infrastructureDir . '/services.%s', $container);
        $this->importConfigWithWildcardSupport($appDir . '/{packages}/*.%s', $container);
        $this->importConfigWithWildcardSupport($appDir . '/{packages}/' . $this->environment . '/*.%s', $container);
        $this->importConfig($appDir . '/services.%s', $container);
    }

    protected function configureRoutes(
        RoutingConfigurator $routes,
    ): void {
        $infrastructureDir = $this->getProjectDir() . '/' . $this->infrastructure . '/config';
        $appDir = $this->getAppConfigDir();

        $this->importInfrastructureRoutesWithOverride($infrastructureDir, $appDir, $routes);
        $this->importRoutesWithWildcardSupport($appDir . '/{routes}/*.%s', $routes);
        $this->importRoutesWithWildcardSupport($appDir . '/{routes}/' . $this->environment . '/*.%s', $routes);
        $this->importRoutes($appDir . '/routes.%s', $routes);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getKernelParameters(): array
    {
        return array_merge(parent::getKernelParameters(), [
            '.kernel.bundles_definition' => $this->getAllBundles(),
            '.kernel.config_dir' => $this->getConfigDir(),
        ]);
    }

    private function extractBaseDirectoryFromWildcard(
        string $wildcardPath,
    ): ?string {
        $wildcardStart = strpos($wildcardPath, '{');

        if (false === $wildcardStart) {
            $wildcardStart = strpos($wildcardPath, '*');
        }

        if (false === $wildcardStart) {
            return null;
        }

        $dirSeparator = strrpos($wildcardPath, '/', $wildcardStart - strlen($wildcardPath));

        if (false === $dirSeparator) {
            return null;
        }

        return substr($wildcardPath, 0, $dirSeparator);
    }

    /**
     * @return array<string, BundleInterface>
     */
    private function getAllBundles(): array
    {
        if (null !== $this->allBundles) {
            return $this->allBundles;
        }

        $this->allBundles = [];

        $infrastructureBundlesFile = $this->getProjectDir() . '/' . $this->infrastructure . '/config/bundles.php';

        if (is_file($infrastructureBundlesFile)) {
            $infrastructureBundles = require $infrastructureBundlesFile;
            $this->allBundles = array_merge($this->allBundles, $infrastructureBundles);
        }

        $appBundlesFile = $this->getAppConfigDir() . '/bundles.php';

        if (is_file($appBundlesFile)) {
            $appBundles = require $appBundlesFile;
            $this->allBundles = array_merge($this->allBundles, $appBundles);
        }

        return $this->allBundles;
    }

    private function importAllConfigs(
        string $dir,
        ContainerConfigurator $container,
    ): void {
        if (!is_dir($dir)) {
            return;
        }

        $files = glob($dir . '/*.php', GLOB_NOSORT) ?: [];
        sort($files);

        foreach ($files as $file) {
            $container->import($file);
        }
    }

    private function importAllRoutes(
        string $dir,
        RoutingConfigurator $routes,
    ): void {
        if (!is_dir($dir)) {
            return;
        }

        $files = glob($dir . '/*.php', GLOB_NOSORT) ?: [];
        sort($files);

        foreach ($files as $file) {
            $routes->import($file);
        }
    }

    private function importConfig(
        string $filename,
        ContainerConfigurator $container,
        bool $check = true,
    ): void {
        $file = sprintf($filename, 'php');

        if (!$check || file_exists($file)) {
            $container->import($file);
        }
    }

    private function importConfigWithWildcardSupport(
        string $filename,
        ContainerConfigurator $container,
    ): void {
        $file = sprintf($filename, 'php');

        // Check if this is a wildcard pattern that needs directory validation
        if ($this->isWildcardPattern($file)) {
            $baseDir = $this->extractBaseDirectoryFromWildcard($file);

            if ($baseDir && is_dir($baseDir)) {
                $container->import($file);
            }
        // If base directory doesn't exist, skip the import silently
        } elseif (file_exists($file)) {
            $container->import($file);
        }
    }

    private function importInfrastructurePackagesWithOverride(
        string $infrastructureConfigDir,
        string $appConfigDir,
        ContainerConfigurator $container,
    ): void {
        $this->importAllConfigs($infrastructureConfigDir . '/packages', $container);
        $this->importAllConfigs($infrastructureConfigDir . '/packages/' . $this->environment, $container);

        $this->importAllConfigs($appConfigDir . '/packages', $container);
        $this->importAllConfigs($appConfigDir . '/packages/' . $this->environment, $container);
    }

    private function importInfrastructureRoutesWithOverride(
        string $infrastructureConfigDir,
        string $appConfigDir,
        RoutingConfigurator $routes,
    ): void {
        $this->importAllRoutes($infrastructureConfigDir . '/routes', $routes);
        $this->importAllRoutes($infrastructureConfigDir . '/routes/' . $this->environment, $routes);

        $this->importAllRoutes($appConfigDir . '/routes', $routes);
        $this->importAllRoutes($appConfigDir . '/routes/' . $this->environment, $routes);

        $infrastructureFile = $infrastructureConfigDir . '/routes.php';

        if (is_file($infrastructureFile)) {
            $routes->import($infrastructureFile);
        }
    }

    private function importRoutes(
        string $filename,
        RoutingConfigurator $routes,
        bool $check = true,
    ): void {
        $file = sprintf($filename, 'php');

        if (!$check || file_exists($file)) {
            $routes->import($file);
        }
    }

    private function importRoutesWithWildcardSupport(
        string $filename,
        RoutingConfigurator $routes,
    ): void {
        $file = sprintf($filename, 'php');

        // Check if this is a wildcard pattern that needs directory validation
        if ($this->isWildcardPattern($file)) {
            $baseDir = $this->extractBaseDirectoryFromWildcard($file);

            if ($baseDir && is_dir($baseDir)) {
                $routes->import($file);
            }
        // If base directory doesn't exist, skip the import silently
        } elseif (file_exists($file)) {
            $routes->import($file);
        }
    }

    private function isWildcardPattern(
        string $filename,
    ): bool {
        return str_contains($filename, '{') || str_contains($filename, '*');
    }

    /**
     * Load app-specific environment files following Symfony's standard hierarchy.
     *
     * Files are loaded in order (later files override earlier ones):
     * 1. /infrastructure/.env                            - Infrastructure environment file
     * 2. /infrastructure/.env.local                      - Local infrastructure overrides (gitignored)
     * 3. /infrastructure/.env.{environment}              - Environment-specific infrastructure (e.g., .env.dev, .env.prod)
     * 4. /infrastructure/.env.{environment}.local        - Environment-specific local infrastructure overrides (gitignored)
     * 5. /apps/{app_id}/.env                     - Base environment file
     * 6. /apps/{app_id}/.env.local               - Local overrides (gitignored)
     * 7. /apps/{app_id}/.env.{environment}       - Environment-specific (e.g., .env.dev, .env.prod)
     * 8. /apps/{app_id}/.env.{environment}.local - Environment-specific local overrides (gitignored)
     *
     * This mirrors the standard Symfony environment loading but for app-specific configuration.
     */
    private function loadAppEnvironmentFiles(): void
    {
        $appDir = $this->getProjectDir() . '/' . $this->apps . '/' . $this->id;
        $infrastructureDir = $this->getProjectDir() . '/' . $this->infrastructure;
        $dotenv = new Dotenv();

        $envFiles = [
            $infrastructureDir . '/.env',
            $infrastructureDir . '/.env.local',
            $infrastructureDir . '/.env.' . $this->environment,
            $infrastructureDir . '/.env.' . $this->environment . '.local',
            $appDir . '/.env',
            $appDir . '/.env.local',
            $appDir . '/.env.' . $this->environment,
            $appDir . '/.env.' . $this->environment . '.local',
        ];

        foreach ($envFiles as $envFile) {
            if (is_file($envFile)) {
                $dotenv->load($envFile);
            }
        }
    }
}
