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

use LogicException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\KernelInterface;
use Valksor\Bundle\Runtime\AppContextResolver;

use function count;
use function explode;
use function implode;
use function is_dir;
use function is_file;
use function scandir;
use function sprintf;
use function str_contains;
use function str_starts_with;

use const SCANDIR_SORT_ASCENDING;

abstract class AbstractConsoleEntrypoint
{
    /**
     * Directory containing applications (relative to project root).
     * Override in child class to customize.
     */
    protected static ?string $appsDir = null;

    /**
     * Directory containing shared infrastructure (relative to project root).
     * Override in child class to customize.
     */
    protected static ?string $infrastructureDir = null;

    /**
     * Creates the console application factory for the Symfony Runtime.
     *
     * @return callable(InputInterface, array<string, mixed>): Application
     */
    public static function create(
        string $projectDir,
    ): callable {
        static::validateDependencies($projectDir);
        static::validateConfiguration();

        return static function (InputInterface $input, array $context) use ($projectDir): Application {
            $requestedAppId = $input->getParameterOption(['--id', '-i'], $context['APP_KERNEL_NAME'] ?? null);
            $commandName = $input->getFirstArgument();
            $isSingleAppCommand = null !== $commandName && str_starts_with($commandName, 'valksor:');

            $availableApps = static::discoverApps($projectDir);

            // Resolve the target app ID
            if (null === $requestedAppId) {
                $appCount = count($availableApps);

                // Multi-app execution stays disabled for dev:* commands so they only target one application
                if (!$isSingleAppCommand && $appCount > 1) {
                    return static::createMultiAppApplication(
                        $availableApps,
                        $context,
                        $projectDir,
                        static::$appsDir,
                        static::$infrastructureDir,
                    );
                }

                if (0 === $appCount) {
                    throw new LogicException('No applications found in appDir');
                }

                $appId = $availableApps[0];
            } elseif (!str_contains($requestedAppId, '.')) {
                $appId = static::resolveAppAlias($requestedAppId, $availableApps);
            } else {
                $appId = $requestedAppId;
            }

            return static::createApplication($appId, $context, $projectDir);
        };
    }

    /**
     * Creates the application kernel.
     */
    abstract protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface;

    /**
     * Creates a multi-app application that runs commands across all apps.
     *
     * @param string[]             $availableApps
     * @param array<string, mixed> $context
     */
    abstract protected static function createMultiAppApplication(
        array $availableApps,
        array $context,
        string $projectDir,
        ?string $appsDir = null,
        ?string $infrastructureDir = null,
    ): MultiAppApplication;

    /**
     * Creates a single-app console application.
     *
     * @param array<string, mixed> $context
     */
    protected static function createApplication(
        string $appId,
        array $context,
        string $projectDir,
    ): Application {
        $context = AppContextResolver::resolve(
            $context,
            $projectDir,
            $appId,
            static::$appsDir,
            static::$infrastructureDir,
        );
        $debugEnabled = AppContextResolver::isDebugEnabled($context);
        $kernel = static::createKernel($context['APP_ENV'], $debugEnabled, $appId);
        $application = new Application($kernel);

        // Add the --id option to the application definition
        $application->getDefinition()->addOption(
            new InputOption('--id', null, InputOption::VALUE_REQUIRED, 'The App ID or alias (omit to run on all apps)', null),
        );

        return $application;
    }

    /**
     * Discovers available applications from the apps directory.
     *
     * @return string[]
     */
    protected static function discoverApps(
        string $projectDir,
    ): array {
        $appsDir = $projectDir . '/' . static::$appsDir;
        $availableApps = [];

        if (is_dir($appsDir)) {
            foreach (scandir($appsDir, SCANDIR_SORT_ASCENDING) as $dir) {
                if ('.' !== $dir && '..' !== $dir && is_dir($appsDir . '/' . $dir)) {
                    $availableApps[] = $dir;
                }
            }
        }

        return $availableApps;
    }

    /**
     * Resolves an app alias to a full app ID.
     *
     * @param string[] $availableApps
     *
     * @throws LogicException
     */
    protected static function resolveAppAlias(
        string $alias,
        array $availableApps,
    ): string {
        $matches = [];

        foreach ($availableApps as $candidate) {
            $candidateAlias = explode('.', $candidate, 2)[0];

            if ($candidateAlias === $alias) {
                $matches[] = $candidate;
            }
        }

        if (1 === count($matches)) {
            return $matches[0];
        }

        if (count($matches) > 1) {
            throw new LogicException(sprintf('Alias "%s" is ambiguous. Matches: %s', $alias, implode(', ', $matches)));
        }

        // Fall back to using the provided value as a full app id
        return $alias;
    }

    /**
     * Validates that configuration is properly set.
     *
     * @throws LogicException
     */
    protected static function validateConfiguration(): void
    {
        if (null === static::$appsDir) {
            throw new LogicException('Apps directory not configured. Set $appsDir property in ' . static::class);
        }

        if (null === static::$infrastructureDir) {
            throw new LogicException('Infrastructure directory not configured. Set $infrastructureDir property in ' . static::class);
        }
    }

    /**
     * Validates that required dependencies are installed.
     *
     * @throws LogicException
     */
    protected static function validateDependencies(
        string $projectDir,
    ): void {
        if (!is_dir($projectDir . '/vendor')) {
            throw new LogicException('Dependencies are missing. Try running "composer install".');
        }

        if (!is_file($projectDir . '/vendor/autoload_runtime.php')) {
            throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
        }
    }
}
