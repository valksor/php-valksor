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

namespace Valksor\Bundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Valksor\Bundle\Command\AutoloadGenerateCommand;

use function file_get_contents;
use function mkdir;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class AutoloadGenerateCommandTest extends TestCase
{
    private string $projectDir;

    /**
     * @throws ExceptionInterface
     */
    public function testExecuteGeneratesAutoloadFileAndOutputsMappings(): void
    {
        $parameters = new ParameterBag([
            'kernel.project_dir' => $this->projectDir,
            'valksor.project.apps_dir' => 'apps',
            'valksor.project.infrastructure_dir' => 'infra',
            'valksor.project.autoload.namespace_prefix' => 'App',
        ]);

        $command = new AutoloadGenerateCommand($parameters);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $command->run($input, $output);

        self::assertSame(AutoloadGenerateCommand::SUCCESS, $result);

        $autoloadFile = $this->projectDir . '/apps/autoload.php';
        self::assertFileExists($autoloadFile);

        $contents = (string) file_get_contents($autoloadFile);
        self::assertStringContainsString('App\\\\App\\\\', $contents);
        self::assertStringContainsString("'/app.main/src/'", $contents);

        $display = $output->fetch();
        self::assertStringContainsString('Autoloader generated successfully!', $display);
        self::assertStringContainsString('App\\App\\ -> app.main/src/', $display);
    }

    protected function setUp(): void
    {
        $this->projectDir = tempnam(sys_get_temp_dir(), 'valksor_autoload_');
        unlink($this->projectDir);
        mkdir($this->projectDir . '/apps/app.main/src', 0o777, true);
        mkdir($this->projectDir . '/apps/app-secondary/no-src', 0o777, true);
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
