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

namespace Valksor\Bundle\Tests\Console;

use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Valksor\Bundle\Console\AbstractConsoleEntrypoint;
use Valksor\Bundle\Console\MultiAppApplication;

use function is_dir;
use function mkdir;
use function scandir;
use function sys_get_temp_dir;
use function tempnam;
use function touch;
use function unlink;

final class AbstractConsoleEntrypointTest extends TestCase
{
    private string $projectDir;

    public function testCreateReturnsMultiAppApplicationWhenNoIdProvided(): void
    {
        FunctionalConsoleEntrypoint::resetState();
        FunctionalMultiAppApplication::resetState();
        FunctionalConsoleEntrypoint::$projectDir = $this->projectDir;

        $factory = FunctionalConsoleEntrypoint::create($this->projectDir);

        $input = new ArrayInput([
            'command' => 'cache:clear',
        ]);
        $context = [
            'APP_ENV' => 'prod',
            'APP_DEBUG' => '0',
        ];

        $application = $factory($input, $context);

        self::assertInstanceOf(FunctionalMultiAppApplication::class, $application);
        self::assertNotEmpty(FunctionalConsoleEntrypoint::$multiAppCalls);
        self::assertSame([
            ['prod', false, 'alpha.main'],
        ], FunctionalMultiAppApplication::$kernelCalls);
    }

    public function testCreateReturnsSingleAppApplication(): void
    {
        FunctionalConsoleEntrypoint::resetState();
        FunctionalMultiAppApplication::resetState();
        FunctionalConsoleEntrypoint::$projectDir = $this->projectDir;

        $factory = FunctionalConsoleEntrypoint::create($this->projectDir);

        $input = new ArrayInput([
            'command' => 'cache:clear',
            '--id' => 'alpha',
        ]);
        $context = [
            'APP_ENV' => 'dev',
            'APP_DEBUG' => '1',
        ];

        $application = $factory($input, $context);

        self::assertInstanceOf(Application::class, $application);
        self::assertSame([
            ['prod', false, 'alpha.main'],
        ], FunctionalConsoleEntrypoint::$kernelCalls);
        self::assertSame([], FunctionalConsoleEntrypoint::$multiAppCalls);
    }

    public function testCreateThrowsWhenConfigurationMissing(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Apps directory not configured. Set $appsDir property in ' . MisconfiguredConsoleEntrypoint::class);

        MisconfiguredConsoleEntrypoint::create($this->projectDir);
    }

    public function testCreateThrowsWhenDependenciesMissing(): void
    {
        $this->removeDirectory($this->vendorPath());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Dependencies are missing. Try running "composer install".');

        FunctionalConsoleEntrypoint::resetState();
        FunctionalConsoleEntrypoint::create($this->projectDir);
    }

    public function testCreateThrowsWhenNoApplicationsFound(): void
    {
        FunctionalConsoleEntrypoint::resetState();
        FunctionalMultiAppApplication::resetState();

        $this->removeDirectory($this->projectDir . '/apps/alpha.main');
        $this->removeDirectory($this->projectDir . '/apps/beta.stage');

        if (!is_dir($this->projectDir . '/apps')) {
            mkdir($this->projectDir . '/apps');
        }

        $factory = FunctionalConsoleEntrypoint::create($this->projectDir);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No applications found in appDir');

        $factory(new ArrayInput(['command' => 'cache:clear']), [
            'APP_ENV' => 'prod',
            'APP_DEBUG' => '0',
        ]);
    }

    public function testCreateThrowsWhenRuntimeMissing(): void
    {
        unlink($this->vendorPath() . '/autoload_runtime.php');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Symfony Runtime is missing. Try running "composer require symfony/runtime".');

        FunctionalConsoleEntrypoint::resetState();
        FunctionalConsoleEntrypoint::create($this->projectDir);
    }

    public function testDiscoverAppsReturnsSortedDirectories(): void
    {
        $apps = TestConsoleEntrypoint::exposeDiscoverApps($this->projectDir);
        self::assertSame(['alpha.main', 'beta.stage'], $apps);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testMultiAppApplicationHandlesListCommand(): void
    {
        FunctionalMultiAppApplication::resetState();
        FunctionalMultiAppApplication::$projectDir = $this->projectDir;

        $application = new FunctionalMultiAppApplication(
            ['alpha.main', 'beta.stage'],
            [
                'APP_ENV' => 'dev',
                'APP_DEBUG' => '1',
            ],
            $this->projectDir,
            'apps',
            'infra',
        );

        $output = new BufferedOutput();
        $exitCode = $application->doRun(new ArrayInput(['command' => 'list']), $output);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Multi-App Console', $output->fetch());
        self::assertSame([
            ['prod', false, 'alpha.main'],
        ], FunctionalMultiAppApplication::$kernelCalls);
    }

    public function testResolveAppAliasMatchesUniqueAlias(): void
    {
        $available = ['alpha.main', 'beta.stage'];
        self::assertSame('alpha.main', TestConsoleEntrypoint::exposeResolveAlias('alpha', $available));
        self::assertSame('beta.stage', TestConsoleEntrypoint::exposeResolveAlias('beta.stage', $available));
    }

    public function testResolveAppAliasThrowsWhenAliasAmbiguous(): void
    {
        $this->expectException(LogicException::class);
        $available = ['alpha.main', 'alpha.extra'];
        TestConsoleEntrypoint::exposeResolveAlias('alpha', $available);
    }

    protected function setUp(): void
    {
        $this->projectDir = tempnam(sys_get_temp_dir(), 'valksor_bundle_');
        unlink($this->projectDir);
        mkdir($this->projectDir);

        mkdir($this->projectDir . '/apps', 0o777, true);
        mkdir($this->projectDir . '/apps/alpha.main', 0o777, true);
        mkdir($this->projectDir . '/apps/beta.stage', 0o777, true);
        mkdir($this->projectDir . '/infra', 0o777, true);
        mkdir($this->projectDir . '/vendor', 0o777, true);
        touch($this->projectDir . '/vendor/autoload_runtime.php');
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->projectDir);
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
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }

    private function vendorPath(): string
    {
        return $this->projectDir . '/vendor';
    }
}

/**
 * @internal
 */
final class TestConsoleEntrypoint extends AbstractConsoleEntrypoint
{
    protected static ?string $appsDir = 'apps';
    protected static ?string $infrastructureDir = 'infra';

    public static function exposeDiscoverApps(
        string $projectDir,
    ): array {
        return self::discoverApps($projectDir);
    }

    public static function exposeResolveAlias(
        string $alias,
        array $available,
    ): string {
        return self::resolveAppAlias($alias, $available);
    }

    protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface {
        throw new LogicException('Kernel creation is not expected during this test.');
    }

    protected static function createMultiAppApplication(
        array $availableApps,
        array $context,
        string $projectDir,
        ?string $appsDir = null,
        ?string $infrastructureDir = null,
    ): MultiAppApplication {
        throw new LogicException('Multi-app application creation is not expected during this test.');
    }
}

/**
 * @internal
 */
final class MisconfiguredConsoleEntrypoint extends AbstractConsoleEntrypoint
{
    protected static ?string $appsDir = null;
    protected static ?string $infrastructureDir = 'infra';

    protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface {
        throw new LogicException('Not required for this test.');
    }

    protected static function createMultiAppApplication(
        array $availableApps,
        array $context,
        string $projectDir,
        ?string $appsDir = null,
        ?string $infrastructureDir = null,
    ): MultiAppApplication {
        throw new LogicException('Not required for this test.');
    }
}

/**
 * @internal
 */
final class FunctionalConsoleEntrypoint extends AbstractConsoleEntrypoint
{
    /** @var array<int, array{0: string, 1: bool, 2: string}> */
    public static array $kernelCalls = [];

    /** @var array<int, array{0: array<int, string>, 1: array<string, mixed>, 2: string, 3: ?string, 4: ?string}> */
    public static array $multiAppCalls = [];

    public static ?string $projectDir = null;
    protected static ?string $appsDir = 'apps';
    protected static ?string $infrastructureDir = 'infra';

    public static function resetState(): void
    {
        self::$projectDir = null;
        self::$kernelCalls = [];
        self::$multiAppCalls = [];
    }

    protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface {
        self::$kernelCalls[] = [$environment, $debug, $appId];

        return new DummyKernel($environment, $debug, $appId, self::$projectDir ?? '');
    }

    protected static function createMultiAppApplication(
        array $availableApps,
        array $context,
        string $projectDir,
        ?string $appsDir = null,
        ?string $infrastructureDir = null,
    ): MultiAppApplication {
        self::$multiAppCalls[] = [$availableApps, $context, $projectDir, $appsDir, $infrastructureDir];
        self::$projectDir = $projectDir;
        FunctionalMultiAppApplication::$projectDir = $projectDir;

        return new FunctionalMultiAppApplication($availableApps, $context, $projectDir, $appsDir, $infrastructureDir);
    }
}

/**
 * @internal
 */
final class FunctionalMultiAppApplication extends MultiAppApplication
{
    /** @var array<int, array{0: string, 1: bool, 2: string}> */
    public static array $kernelCalls = [];
    public static ?string $projectDir = null;

    public static function resetState(): void
    {
        self::$projectDir = null;
        self::$kernelCalls = [];
    }

    protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface {
        self::$kernelCalls[] = [$environment, $debug, $appId];

        return new DummyKernel($environment, $debug, $appId, self::$projectDir ?? '');
    }
}

/**
 * @internal
 */
final class DummyKernel implements KernelInterface
{
    private bool $booted = false;
    private ContainerBuilder $container;
    private float $startTime;

    public function __construct(
        private readonly string $environment,
        private readonly bool $debug,
        private readonly string $appId,
        private readonly string $projectDir,
    ) {
        $this->startTime = microtime(true);
        $this->container = new ContainerBuilder();
        $this->container->set('event_dispatcher', new EventDispatcher());
        $this->container->setParameter('console.command.ids', []);
        $this->container->setParameter('console.lazy_command.ids', []);
    }

    public function boot(): void
    {
        $this->booted = true;
    }

    public function getBuildDir(): string
    {
        return $this->getProjectDir() . '/var/build/' . $this->environment;
    }

    public function getBundle(
        string $name,
    ): BundleInterface {
        throw new LogicException('No bundles registered.');
    }

    public function getBundles(): array
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getCharset(): string
    {
        return 'UTF-8';
    }

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function getProjectDir(): string
    {
        return '' !== $this->projectDir ? $this->projectDir : sys_get_temp_dir();
    }

    public function getShareDir(): string
    {
        return $this->getProjectDir() . '/var/share';
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }

    public function handle(
        Request $request,
        int $type = self::MAIN_REQUEST,
        bool $catch = true,
    ): Response {
        return new Response('OK');
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function locateResource(
        string $name,
    ): string {
        return $name;
    }

    public function registerBundles(): iterable
    {
        return [];
    }

    public function registerContainerConfiguration(
        LoaderInterface $loader,
    ): void {
    }

    public function shutdown(): void
    {
        $this->booted = false;
    }
}
