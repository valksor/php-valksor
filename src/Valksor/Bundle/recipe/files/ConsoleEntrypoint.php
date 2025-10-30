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

namespace App\Console;

use App\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Valksor\Bundle\Console\AbstractConsoleEntrypoint;
use Valksor\Bundle\Console\MultiAppApplication as BaseMultiAppApplication;

class ConsoleEntrypoint extends AbstractConsoleEntrypoint
{
    protected static ?string $appsDir;
    protected static ?string $infrastructureDir;

    protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface {
        return new Kernel($environment, $debug, $appId);
    }

    protected static function createMultiAppApplication(
        array $availableApps,
        array $context,
        string $projectDir,
        ?string $appsDir = null,
        ?string $infrastructureDir = null,
    ): BaseMultiAppApplication {
        return new MultiAppApplication($availableApps, $context, $projectDir, $appsDir, $infrastructureDir);
    }
}
