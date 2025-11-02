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
use Symfony\Component\HttpKernel\KernelInterface;
use Valksor\Bundle\Console\AbstractConsoleEntrypoint;
use Valksor\Bundle\Console\MultiAppApplication;

use function mkdir;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class AbstractConsoleEntrypointTest extends TestCase
{
    private string $projectDir;

    public function testDiscoverAppsReturnsSortedDirectories(): void
    {
        $apps = TestConsoleEntrypoint::exposeDiscoverApps($this->projectDir);
        self::assertSame(['alpha.main', 'beta.stage'], $apps);
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
        $this->projectDir = tempnam(sys_get_temp_dir(), 'valksor_apps_');
        unlink($this->projectDir);
        mkdir($this->projectDir . '/apps/alpha.main', 0o777, true);
        mkdir($this->projectDir . '/apps/beta.stage', 0o777, true);
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
