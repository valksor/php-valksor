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

namespace Valksor\Bundle\Console;

use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Valksor\Bundle\Runtime\AppContextResolver;

use function array_map;
use function explode;
use function implode;

abstract class MultiAppApplication extends Application
{
    /**
     * @param string[]             $availableApps
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly array $availableApps,
        private readonly array $context,
        private readonly string $projectDir,
        private readonly ?string $appsDir = null,
        private readonly ?string $infrastructureDir = null,
    ) {
        $firstAppId = $availableApps[0] ?? throw new LogicException('No applications registered for MultiAppApplication.');
        $resolvedContext = AppContextResolver::resolve(
            $context,
            $projectDir,
            $firstAppId,
            $appsDir,
            $infrastructureDir,
        );
        $kernel = static::createKernel(
            $resolvedContext['APP_ENV'],
            AppContextResolver::isDebugEnabled($resolvedContext),
            $firstAppId,
        );
        parent::__construct($kernel);
    }

    /**
     * @throws ExceptionInterface
     */
    public function doRun(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $io = new SymfonyStyle($input, $output);
        $commandName = $input->getFirstArgument();

        if (null === $commandName || 'list' === $commandName) {
            $io->title('Multi-App Console');
            $io->note('No --id specified. Available apps: ' . implode(', ', array_map([$this, 'alias'], $this->availableApps)));

            return parent::doRun($input, $output);
        }

        $exitCode = 0;

        foreach ($this->availableApps as $appId) {
            $io->section("Running '$commandName' for app: " . $this->alias($appId));

            try {
                $appContext = AppContextResolver::resolve(
                    $this->context,
                    $this->projectDir,
                    $appId,
                    $this->appsDir,
                    $this->infrastructureDir,
                );
                $kernel = static::createKernel(
                    $appContext['APP_ENV'],
                    AppContextResolver::isDebugEnabled($appContext),
                    $appId,
                );
                $application = new Application($kernel);

                // Special-case: prevent later app runs from cleaning assets published by earlier runs
                if ('assets:install' === $commandName) {
                    $args = ['command' => $commandName];

                    // Preserve common flags from the original input
                    if ($input->hasParameterOption('--relative')) {
                        $args['--relative'] = true;
                    }

                    if ($input->hasParameterOption('--symlink')) {
                        $args['--symlink'] = true;
                    }

                    if ($input->hasParameterOption('--no-interaction')) {
                        $args['--no-interaction'] = true;
                    }

                    // Always avoid cleanup across apps to keep previously-installed assets
                    $args['--no-cleanup'] = true;

                    $result = $application->find($commandName)->run(new ArrayInput($args), $output);
                } else {
                    $result = $application->doRun($input, $output);
                }

                if (0 !== $result) {
                    $exitCode = $result;
                    $io->error("Command failed for app '" . $this->alias($appId) . "' with exit code $result");
                } else {
                    $io->success("Command completed successfully for app '" . $this->alias($appId) . "'");
                }
            } catch (Exception $e) {
                $exitCode = 1;
                $io->error("Error running command for app '" . $this->alias($appId) . "': " . $e->getMessage());
            }
        }

        if (0 === $exitCode) {
            $io->success('Command completed successfully for all apps');
        } else {
            $io->error('Command failed for one or more apps');
        }

        return $exitCode;
    }

    /**
     * Creates the application kernel.
     */
    abstract protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface;

    private function alias(
        string $appId,
    ): string {
        return explode('.', $appId, 2)[0];
    }
}
