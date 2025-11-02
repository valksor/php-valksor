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
use Valksor\Bundle\Kernel\AbstractKernel;

use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function scandir;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

if (!class_exists('Symfony\\Component\\Dotenv\\Dotenv')) {
    require __DIR__ . '/../Fixtures/DotenvStub.php';
}

final class AbstractKernelTest extends TestCase
{
    private string $projectDir;

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
    public function testListInfrastructureFilesWithOverrideSkipsOverriddenFiles(): void
    {
        mkdir($this->projectDir . '/infra/config/routes', 0o777, true);
        mkdir($this->projectDir . '/apps/app.main/config/routes', 0o777, true);

        file_put_contents($this->projectDir . '/infra/config/routes/a.php', '<?php');
        file_put_contents($this->projectDir . '/infra/config/routes/b.php', '<?php');
        file_put_contents($this->projectDir . '/apps/app.main/config/routes/b.override.php', '<?php');

        $kernel = new TestKernel($this->projectDir, 'app.main', 'dev');

        $method = new ReflectionMethod(AbstractKernel::class, 'listInfrastructureFilesWithOverride');
        $method->setAccessible(true);

        $files = $method->invoke($kernel, $this->projectDir . '/infra/config/routes', $this->projectDir . '/apps/app.main/config/routes');

        self::assertCount(1, $files);
        self::assertStringContainsString('a.php', $files[0]);
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

    public function getProjectDir(): string
    {
        return $this->projectDirOverride;
    }
}
