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
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use function array_any;
use function array_merge;
use function explode;
use function file_exists;
use function glob;
use function is_dir;
use function is_file;
use function pathinfo;
use function sort;
use function sprintf;
use function ucfirst;

use const GLOB_NOSORT;
use const PATHINFO_FILENAME;

abstract class AbstractKernel extends BaseKernel
{
    use MicroKernelTrait;
    protected ?string $appsDir = null;

    protected ?string $infrastructureDir = null;

    private ?array $allBundles = null;

    public function __construct(
        string $environment,
        bool $debug,
        private readonly string $id,
    ) {
        $_SERVER['APP_KERNEL_NAME'] = $this->id;
        $_ENV['APP_KERNEL_NAME'] = $this->id;

        if (null === $this->appsDir) {
            throw new LogicException('Apps dir not set');
        }

        if (null === $this->infrastructureDir) {
            throw new LogicException('Infrastructure dir not set');
        }

        parent::__construct($environment, $debug);

        // Load app-specific .env files
        $this->loadAppEnvironmentFiles();
    }

    public function getAppConfigDir(): string
    {
        return $this->getProjectDir() . '/' . $this->appsDir . '/' . $this->id . '/config';
    }

    public function getCacheDir(): string
    {
        return (($_SERVER['APP_CACHE_DIR'] ?? $this->getProjectDir()) . '/var/cache') . '/' . $this->id . '/' . $this->environment;
    }

    public function getConfigDir(): string
    {
        return $this->getProjectDir() . '/' . $this->infrastructureDir . '/config';
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
        $infrastructureDir = $this->getProjectDir() . '/' . $this->infrastructureDir . '/config';
        $appDir = $this->getAppConfigDir();

        // Set app-specific parameters
        $container->parameters()
            ->set('app.id', $this->id)
            ->set('app.namespace', ucfirst(explode('.', $this->id, 2)[0]));

        $this->importInfrastructurePackagesWithOverride($infrastructureDir, $appDir, $container);
        $this->importConfig($infrastructureDir . '/services.%s', $container);
        $this->importConfig($appDir . '/{packages}/*.%s', $container, false);
        $this->importConfig($appDir . '/{packages}/' . $this->environment . '/*.%s', $container, false);
        $this->importConfig($appDir . '/services.%s', $container);
    }

    protected function configureRoutes(
        RoutingConfigurator $routes,
    ): void {
        $infrastructureDir = $this->getProjectDir() . '/' . $this->infrastructureDir . '/config';
        $appDir = $this->getAppConfigDir();

        $this->importInfrastructureRoutesWithOverride($infrastructureDir, $appDir, $routes);
        // Import app routes normally
        $this->importRoutes($appDir . '/{routes}/*.%s', $routes, false);
        $this->importRoutes($appDir . '/{routes}/' . $this->environment . '/*.%s', $routes, false);
        $this->importRoutes($appDir . '/routes.%s', $routes);
    }

    protected function getKernelParameters(): array
    {
        return array_merge(parent::getKernelParameters(), [
            '.kernel.bundles_definition' => $this->getAllBundles(),
            '.kernel.config_dir' => $this->getConfigDir(),
        ]);
    }

    private function getAllBundles(): array
    {
        if (null !== $this->allBundles) {
            return $this->allBundles;
        }

        $this->allBundles = [];

        $infrastructureBundlesFile = $this->getProjectDir() . '/' . $this->infrastructureDir . '/config/bundles.php';

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

    private function hasOverride(
        string $dir,
        string $base,
    ): bool {
        return array_any(['php', 'yaml'], static fn ($ext) => is_file($dir . '/' . $base . '.override.' . $ext));
    }

    private function importConfig(
        string $filename,
        ContainerConfigurator $container,
        bool $check = true,
    ): void {
        foreach (['yaml', 'php'] as $ext) {
            $file = sprintf($filename, $ext);

            if (!$check || file_exists($file)) {
                $container->import($file);
            }
        }
    }

    private function importDirConfigsWithOverride(
        string $infrastructureDir,
        string $appDir,
        ContainerConfigurator $container,
    ): void {
        foreach ($this->listInfrastructureFilesWithOverride($infrastructureDir, $appDir) as $infrastructureFile) {
            $container->import($infrastructureFile);
        }
    }

    private function importDirRoutesWithOverride(
        string $infrastructureDir,
        string $appDir,
        RoutingConfigurator $routes,
    ): void {
        foreach ($this->listInfrastructureFilesWithOverride($infrastructureDir, $appDir) as $infrastructureFile) {
            $routes->import($infrastructureFile);
        }
    }

    private function importInfrastructurePackagesWithOverride(
        string $infrastructureConfigDir,
        string $appConfigDir,
        ContainerConfigurator $container,
    ): void {
        $this->importDirConfigsWithOverride(
            $infrastructureConfigDir . '/packages',
            $appConfigDir . '/packages',
            $container,
        );

        $this->importDirConfigsWithOverride(
            $infrastructureConfigDir . '/packages/' . $this->environment,
            $appConfigDir . '/packages/' . $this->environment,
            $container,
        );
    }

    private function importInfrastructureRoutesWithOverride(
        string $infrastructureConfigDir,
        string $appConfigDir,
        RoutingConfigurator $routes,
    ): void {
        $this->importDirRoutesWithOverride(
            $infrastructureConfigDir . '/routes',
            $appConfigDir . '/routes',
            $routes,
        );

        $this->importDirRoutesWithOverride(
            $infrastructureConfigDir . '/routes/' . $this->environment,
            $appConfigDir . '/routes/' . $this->environment,
            $routes,
        );

        foreach (['yaml', 'php'] as $ext) {
            $infrastructureFile = $infrastructureConfigDir . '/routes.' . $ext;

            if (!is_file($infrastructureFile)) {
                continue;
            }

            if ($this->hasOverride($appConfigDir, 'routes')) {
                continue;
            }

            $routes->import($infrastructureFile);
        }
    }

    private function importRoutes(
        string $filename,
        RoutingConfigurator $routes,
        bool $check = true,
    ): void {
        foreach (['yaml', 'php'] as $ext) {
            $file = sprintf($filename, $ext);

            if (!$check || file_exists($file)) {
                $routes->import($file);
            }
        }
    }

    /**
     * Build a sorted list of infrastructure config files (php|yaml) from a directory,
     * excluding any whose base names are overridden by app-level override files.
     * Results are sorted alphabetically.
     *
     * @return array<string>
     */
    private function listInfrastructureFilesWithOverride(
        string $infrastructureDir,
        string $appDir,
    ): array {
        if (!is_dir($infrastructureDir)) {
            return [];
        }

        $files = array_merge(
            glob($infrastructureDir . '/*.php', GLOB_NOSORT) ?: [],
            glob($infrastructureDir . '/*.yaml', GLOB_NOSORT) ?: [],
        );
        sort($files);

        $result = [];

        foreach ($files as $infrastructureFile) {
            $base = pathinfo($infrastructureFile, PATHINFO_FILENAME);

            if ($this->hasOverride($appDir, $base)) {
                continue;
            }
            $result[] = $infrastructureFile;
        }

        return $result;
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
        $appDir = $this->getProjectDir() . '/' . $this->appsDir . '/' . $this->id;
        $infrastructureDir = $this->getProjectDir() . '/' . $this->infrastructureDir;
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
