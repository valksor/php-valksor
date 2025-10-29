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

namespace Valksor\Bundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use function ltrim;

abstract class AbstractCommand extends Command
{
    protected string $projectDir;

    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
    ) {
        parent::__construct();
        $this->projectDir = $parameterBag->get('kernel.project_dir');
    }

    /**
     * Create SymfonyStyle instance.
     */
    protected function createSymfonyStyle(
        InputInterface $input,
        $output,
    ): SymfonyStyle {
        return new SymfonyStyle($input, $output);
    }

    protected function getAppsDir(): string
    {
        $appsDir = $this->parameterBag->get('valksor.project.apps_dir');

        return $this->resolveProjectRoot() . '/' . ltrim($appsDir, '/');
    }

    protected function getInfrastructureDir(): string
    {
        $infrastructureDir = $this->parameterBag->get('valksor.project.infrastructure_dir');

        return $this->resolveProjectRoot() . '/' . ltrim($infrastructureDir, '/');
    }

    protected function handleCommandError(
        string $message,
        ?SymfonyStyle $io = null,
    ): int {
        $io?->error($message);

        return Command::FAILURE;
    }

    /**
     * Handle command success.
     */
    protected function handleCommandSuccess(
        string $message = 'Command completed successfully!',
        ?SymfonyStyle $io = null,
    ): int {
        $io?->success($message);

        return Command::SUCCESS;
    }

    protected function isProductionEnvironment(): bool
    {
        return 'prod' === ($_ENV['APP_ENV'] ?? 'prod');
    }

    protected function resolveProjectRoot(): string
    {
        return $this->parameterBag->get('kernel.project_dir');
    }
}
