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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Valksor\Bundle\Command\AbstractCommand;

final class AbstractCommandTest extends TestCase
{
    private ParameterBag $parameters;

    public function testCreateSymfonyStyleUsesProvidedStreams(): void
    {
        $command = new class($this->parameters) extends AbstractCommand {
            public function exposeCreateSymfonyStyle(
                ArrayInput $input,
                BufferedOutput $output,
            ): SymfonyStyle {
                return $this->createSymfonyStyle($input, $output);
            }
        };

        $this->expectNotToPerformAssertions();
        $command->exposeCreateSymfonyStyle(new ArrayInput([]), new BufferedOutput());
    }

    public function testDirectoryHelpersResolveAgainstProjectRoot(): void
    {
        $command = new class($this->parameters) extends AbstractCommand {
            public function exposeAppsDir(): string
            {
                return $this->getAppsDir();
            }

            public function exposeInfrastructureDir(): string
            {
                return $this->getInfrastructureDir();
            }
        };

        self::assertSame('/project/apps', $command->exposeAppsDir());
        self::assertSame('/project/infra', $command->exposeInfrastructureDir());
    }

    public function testHandleCommandSuccessAndFailureWriteMessages(): void
    {
        $command = new class($this->parameters) extends AbstractCommand {
            public function exposeHandleSuccess(
                SymfonyStyle $io,
            ): int {
                return $this->handleCommandSuccess('done', $io);
            }

            public function exposeHandleFailure(
                SymfonyStyle $io,
            ): int {
                return $this->handleCommandError('error', $io);
            }
        };

        $output = new BufferedOutput();
        $io = new SymfonyStyle(new ArrayInput([]), $output);

        self::assertSame(AbstractCommand::SUCCESS, $command->exposeHandleSuccess($io));
        self::assertSame(AbstractCommand::FAILURE, $command->exposeHandleFailure($io));

        $display = $output->fetch();
        self::assertStringContainsString('done', $display);
        self::assertStringContainsString('error', $display);
    }

    public function testIsProductionEnvironmentDefaultsToProd(): void
    {
        $backup = $_ENV['APP_ENV'] ?? null;

        try {
            unset($_ENV['APP_ENV']);
            $command = new class($this->parameters) extends AbstractCommand {
                public function exposeIsProduction(): bool
                {
                    return $this->isProductionEnvironment();
                }
            };

            self::assertTrue($command->exposeIsProduction());

            $_ENV['APP_ENV'] = 'dev';
            self::assertFalse($command->exposeIsProduction());
        } finally {
            if (null === $backup) {
                unset($_ENV['APP_ENV']);
            } else {
                $_ENV['APP_ENV'] = $backup;
            }
        }
    }

    protected function setUp(): void
    {
        $this->parameters = new ParameterBag([
            'kernel.project_dir' => '/project',
            'valksor.project.apps_dir' => 'apps',
            'valksor.project.infrastructure_dir' => 'infra',
        ]);
    }
}
