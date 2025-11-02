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

namespace Valksor\Bundle\Tests\Runtime;

use PHPUnit\Framework\TestCase;
use Valksor\Bundle\Runtime\AppContextResolver;

use function file_put_contents;
use function mkdir;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

if (!class_exists('Symfony\\Component\\Dotenv\\Dotenv')) {
    require __DIR__ . '/../Fixtures/DotenvStub.php';
}

final class AppContextResolverTest extends TestCase
{
    private array $originalEnv = [];
    private array $originalServer = [];
    private ?string $originalVars = null;
    private string $projectDir;

    public function testIsDebugEnabledSupportsVariousRepresentations(): void
    {
        self::assertTrue(AppContextResolver::isDebugEnabled(['APP_DEBUG' => 'yes']));
        self::assertFalse(AppContextResolver::isDebugEnabled(['APP_DEBUG' => 'off']));
        self::assertTrue(AppContextResolver::isDebugEnabled(['APP_DEBUG' => 1]));
        self::assertFalse(AppContextResolver::isDebugEnabled(['APP_DEBUG' => 0]));
    }

    public function testResolveAppliesEnvironmentOverridesFromFiles(): void
    {
        file_put_contents($this->projectDir . '/infra/.env', "APP_ENV=prod\n");
        file_put_contents($this->projectDir . '/apps/blog.main/.env', "APP_DEBUG=1\n");

        $context = [
            'APP_ENV' => 'dev',
            'APP_DEBUG' => '0',
        ];

        $resolved = AppContextResolver::resolve(
            $context,
            $this->projectDir,
            'blog.main',
            'apps',
            'infra',
        );

        self::assertSame('prod', $resolved['APP_ENV']);
        self::assertSame('1', $resolved['APP_DEBUG']);

        self::assertSame('prod', $_ENV['APP_ENV']);
        self::assertSame('1', $_ENV['APP_DEBUG']);
        self::assertSame('prod', $_SERVER['APP_ENV']);
        self::assertSame('1', $_SERVER['APP_DEBUG']);
    }

    public function testResolveDefaultsToProdWhenContextIsDev(): void
    {
        $resolved = AppContextResolver::resolve(
            ['APP_ENV' => 'dev', 'APP_DEBUG' => '0'],
            $this->projectDir,
            'blog.main',
            'apps',
            'infra',
        );

        self::assertSame('prod', $resolved['APP_ENV']);
        self::assertSame('0', $resolved['APP_DEBUG']);
    }

    protected function setUp(): void
    {
        $this->originalEnv = $_ENV;
        $this->originalServer = $_SERVER;
        $this->originalVars = $_ENV['SYMFONY_DOTENV_VARS'] ?? null;

        $this->projectDir = tempnam(sys_get_temp_dir(), 'valksor_context_');
        unlink($this->projectDir);
        mkdir($this->projectDir . '/infra', 0o777, true);
        mkdir($this->projectDir . '/apps/blog.main', 0o777, true);
    }

    protected function tearDown(): void
    {
        $_ENV = $this->originalEnv;
        $_SERVER = $this->originalServer;

        if (null === $this->originalVars) {
            unset($_ENV['SYMFONY_DOTENV_VARS'], $_SERVER['SYMFONY_DOTENV_VARS']);
        } else {
            $_ENV['SYMFONY_DOTENV_VARS'] = $_SERVER['SYMFONY_DOTENV_VARS'] = $this->originalVars;
        }

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
