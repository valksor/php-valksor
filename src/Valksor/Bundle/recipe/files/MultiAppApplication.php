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
use Valksor\Bundle\Console\MultiAppApplication as BaseMultiAppApplication;

class MultiAppApplication extends BaseMultiAppApplication
{
    protected static function createKernel(
        string $environment,
        bool $debug,
        string $appId,
    ): KernelInterface {
        return new Kernel($environment, $debug, $appId);
    }
}
