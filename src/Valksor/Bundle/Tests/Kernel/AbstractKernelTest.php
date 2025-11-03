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

namespace Valksor\Bundle\Tests\Kernel;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader as DiPhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Loader\PhpFileLoader as RoutingPhpFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Valksor\Bundle\Kernel\AbstractKernel;

use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function scandir;
use function sort;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

if (!class_exists('Symfony\\Component\\Dotenv\\Dotenv')) {
    require __DIR__ . '/../Fixtures/DotenvStub.php';
}

final class AbstractKernelTest extends TestCase
{
    private string $projectDir;

    public function testConfigureContainerImportsInfrastructureAndAppConfigs(): void
    {
        mkdir($this->projectDir . '/infra/config/packages/dev', 0o777, true);
        mkdir($this->projectDir . '/apps/app.main/config/packages/dev', 0o777, true);

        file_put_contents($this->projectDir . '/infra/config/packages/foo.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/packages/bar.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/packages/bar.override.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/packages/dev/env.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/packages/dev/skip.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/packages/dev/skip.override.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/services.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/services.php', '<?php');

        $kernel = new TestKernel($this->projectDir, 'app.main', 'dev');

        $imports = [];
        $configurator = $this->createContainerConfigurator($imports);
        $kernel->exposeConfigureContainer($configurator);

        $expected = [
            $this->projectDir . '/infra/config/packages/foo.php',
            $this->projectDir . '/infra/config/packages/dev/env.php',
            $this->projectDir . '/infra/config/services.php',
            $this->projectDir . '/apps/app.main/config/{packages}/*.php',
            $this->projectDir . '/apps/app.main/config/{packages}/dev/*.php',
            $this->projectDir . '/apps/app.main/config/services.php',
        ];

        sort($imports);
        sort($expected);

        self::assertSame($expected, $imports);
    }

    public function testConfigureRoutesRespectsOverrides(): void
    {
        mkdir($this->projectDir . '/infra/config/routes/dev', 0o777, true);
        mkdir($this->projectDir . '/apps/app.main/config/routes/dev', 0o777, true);

        file_put_contents($this->projectDir . '/infra/config/routes/alpha.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/routes/beta.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/routes/beta.override.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/routes/dev/gamma.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/routes/dev/delta.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/routes/dev/delta.override.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/routes.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/routes.override.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/routes.php', '<?php');

        $kernel = new TestKernel($this->projectDir, 'app.main', 'dev');

        $imports = [];
        $routesConfigurator = $this->createRoutingConfigurator($imports, $kernel->getEnvironment());
        $kernel->exposeConfigureRoutes($routesConfigurator);

        $expected = [
            $this->projectDir . '/infra/config/routes/alpha.php',
            $this->projectDir . '/infra/config/routes/dev/gamma.php',
            $this->projectDir . '/apps/app.main/config/{routes}/*.php',
            $this->projectDir . '/apps/app.main/config/{routes}/dev/*.php',
            $this->projectDir . '/apps/app.main/config/routes.php',
        ];

        sort($imports);
        sort($expected);

        self::assertSame($expected, $imports);
    }

    public function testDirectoryHelpersRespectServerOverrides(): void
    {
        $_SERVER['APP_CACHE_DIR'] = $this->projectDir . '/tmp';
        $_SERVER['APP_LOG_DIR'] = $this->projectDir . '/logs';

        $kernel = new TestKernel($this->projectDir, 'app.main', 'test');

        self::assertSame($this->projectDir . '/apps/app.main/config', $kernel->getAppConfigDir());
        self::assertSame($this->projectDir . '/infra/config', $kernel->getConfigDir());
        self::assertSame($this->projectDir . '/logs/var/log/app.main', $kernel->getLogDir());
        self::assertSame($this->projectDir . '/tmp/var/cache/app.main/test', $kernel->getCacheDir());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetAllBundlesMergesInfrastructureAndAppDefinitions(): void
    {
        file_put_contents($this->projectDir . '/infra/config/bundles.php', '<?php return ["Infra\\\\Bundle" => ["all" => true]];');
        file_put_contents($this->projectDir . '/apps/app.main/config/bundles.php', '<?php return ["App\\\\Bundle" => ["dev" => true]];');

        $kernel = new TestKernel($this->projectDir, 'app.main', 'dev');

        $method = new ReflectionMethod(AbstractKernel::class, 'getAllBundles');

        $bundles = $method->invoke($kernel);

        self::assertArrayHasKey('Infra\\Bundle', $bundles);
        self::assertArrayHasKey('App\\Bundle', $bundles);
        self::assertSame($bundles, $method->invoke($kernel));
    }

    /**
     * @throws ReflectionException
     */
    public function testGetKernelParametersIncludesBundlesDefinition(): void
    {
        file_put_contents($this->projectDir . '/infra/config/bundles.php', '<?php return ["Infra\\\\Bundle" => ["all" => true]];');

        $kernel = new TestKernel($this->projectDir, 'app.main', 'prod');

        $parameters = new ReflectionMethod(AbstractKernel::class, 'getKernelParameters')->invoke($kernel);

        self::assertSame($this->projectDir . '/infra/config', $parameters['.kernel.config_dir']);
        self::assertArrayHasKey('Infra\\Bundle', $parameters['.kernel.bundles_definition']);
    }

    /**
     * @throws ReflectionException
     */
    public function testListInfrastructureFilesWithOverrideSkipsOverriddenFiles(): void
    {
        mkdir($this->projectDir . '/infra/config/routes', 0o777, true);
        mkdir($this->projectDir . '/apps/app.main/config/routes', 0o777, true);

        file_put_contents($this->projectDir . '/infra/config/routes/a.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/routes/b.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/routes/b.override.php', '<?php');

        $kernel = new TestKernel($this->projectDir, 'app.main', 'dev');

        $method = new ReflectionMethod(AbstractKernel::class, 'listInfrastructureFilesWithOverride');

        $files = $method->invoke($kernel, $this->projectDir . '/infra/config/routes', $this->projectDir . '/apps/app.main/config/routes');

        self::assertCount(1, $files);
        self::assertStringContainsString('a.php', $files[0]);
    }

    public function testLoadAppEnvironmentFilesRespectsPrecedence(): void
    {
        $infraDir = $this->projectDir . '/infra';
        $appDir = $this->projectDir . '/apps/app.main';

        file_put_contents($infraDir . '/.env', 'FOO=infra');
        file_put_contents($infraDir . '/.env.local', 'FOO=infra_local');
        file_put_contents($infraDir . '/.env.dev', 'FOO=infra_dev');
        file_put_contents($infraDir . '/.env.dev.local', 'FOO=infra_dev_local');
        file_put_contents($appDir . '/.env', 'FOO=app');
        file_put_contents($appDir . '/.env.local', 'FOO=app_local');
        file_put_contents($appDir . '/.env.dev', 'FOO=app_dev');
        file_put_contents($appDir . '/.env.dev.local', 'FOO=app_dev_local');

        unset($_ENV['FOO'], $_SERVER['FOO']);

        new TestKernel($this->projectDir, 'app.main', 'dev');

        self::assertSame('app_dev_local', $_ENV['FOO']);
        self::assertSame('app_dev_local', $_SERVER['FOO']);

        unset($_ENV['FOO'], $_SERVER['FOO']);
    }

    public function testRegisterBundlesFiltersByEnvironment(): void
    {
        file_put_contents($this->projectDir . '/infra/config/bundles.php', '<?php return [
            "Valksor\\\\Bundle\\\\Tests\\\\Kernel\\\\AllTestBundle" => ["all" => true],
            "Valksor\\\\Bundle\\\\Tests\\\\Kernel\\\\DevTestBundle" => ["dev" => true],
            "Valksor\\\\Bundle\\\\Tests\\\\Kernel\\\\ProdTestBundle" => ["prod" => true],
        ];');

        $kernel = new TestKernel($this->projectDir, 'app.main', 'dev');

        $bundleClasses = [];

        foreach ($kernel->registerBundles() as $bundle) {
            $bundleClasses[] = $bundle::class;
        }

        sort($bundleClasses);

        self::assertSame([
            AllTestBundle::class,
            DevTestBundle::class,
        ], $bundleClasses);
    }

    protected function setUp(): void
    {
        $this->projectDir = tempnam(sys_get_temp_dir(), 'valksor_kernel_');
        unlink($this->projectDir);
        mkdir($this->projectDir . '/apps/app.main/config', 0o777, true);
        mkdir($this->projectDir . '/infra/config', 0o777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->projectDir);
        unset($_SERVER['APP_CACHE_DIR'], $_SERVER['APP_LOG_DIR']);
    }

    /**
     * @param array<int, string> $imports
     */
    private function createContainerConfigurator(
        array &$imports,
    ): ContainerConfigurator {
        $builder = new ContainerBuilder();
        $locator = new class implements FileLocatorInterface {
            public function locate(
                string $name,
                ?string $currentPath = null,
                bool $first = true,
            ): string {
                return $name;
            }
        };

        $loader = new RecordingContainerPhpFileLoader($builder, $locator, $imports);
        $instanceof = [];

        return new ContainerConfigurator($builder, $loader, $instanceof, __FILE__, 'kernel_test.php');
    }

    /**
     * @param array<int, string> $imports
     */
    private function createRoutingConfigurator(
        array &$imports,
        string $environment,
    ): RoutingConfigurator {
        $collection = new RouteCollection();
        $locator = new class implements FileLocatorInterface {
            public function locate(
                string $name,
                ?string $currentPath = null,
                bool $first = true,
            ): string {
                return $name;
            }
        };

        $loader = new RecordingRoutingPhpFileLoader($locator, $imports);

        return new RoutingConfigurator($collection, $loader, __FILE__, __FILE__, $environment);
    }

    private function removeDirectory(
        string $directory,
    ): void {
        if (!is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }

            $path = $directory . '/' . $item;

            if (is_dir($path)) {
                $this->removeDirectory($path);
            } elseif (is_file($path)) {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}

/**
 * @internal
 */
final class RecordingContainerPhpFileLoader extends DiPhpFileLoader
{
    /** @var array<int, string> */
    private array $imports;

    /**
     * @param array<int, string> $imports
     */
    public function __construct(
        ContainerBuilder $container,
        FileLocatorInterface $locator,
        array &$imports,
    ) {
        parent::__construct($container, $locator);
        $this->imports = &$imports;
    }

    public function import(
        mixed $resource,
        ?string $type = null,
        string|bool $ignoreErrors = false,
        ?string $sourceResource = null,
        mixed $exclude = null,
    ): mixed {
        $this->imports[] = (string) $resource;

        return null;
    }
}

/**
 * @internal
 */
final class RecordingRoutingPhpFileLoader extends RoutingPhpFileLoader
{
    /** @var array<int, string> */
    private array $imports;

    /**
     * @param array<int, string> $imports
     */
    public function __construct(
        FileLocatorInterface $locator,
        array &$imports,
    ) {
        parent::__construct($locator);
        $this->imports = &$imports;
    }

    public function import(
        mixed $resource,
        ?string $type = null,
        string|bool $ignoreErrors = false,
        ?string $sourceResource = null,
        mixed $exclude = null,
    ): array {
        $this->imports[] = (string) $resource;

        return [];
    }
}

/**
 * @internal
 */
final class TestKernel extends AbstractKernel
{
    protected ?string $apps = 'apps';
    protected ?string $infrastructure = 'infra';

    public function __construct(
        private readonly string $projectDirOverride,
        string $id,
        string $environment,
    ) {
        parent::__construct($environment, true, $id);
    }

    public function exposeConfigureContainer(
        ContainerConfigurator $configurator,
    ): void {
        $this->configureContainer($configurator);
    }

    public function exposeConfigureRoutes(
        RoutingConfigurator $routes,
    ): void {
        $this->configureRoutes($routes);
    }

    public function getProjectDir(): string
    {
        return $this->projectDirOverride;
    }
}

final class AllTestBundle extends Bundle
{
}

final class DevTestBundle extends Bundle
{
}

final class ProdTestBundle extends Bundle
{
}
