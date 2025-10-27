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

namespace Valksor\Functions\Local\Traits;

use function array_map;
use function glob;
use function is_dir;
use function rmdir;
use function unlink;

use const GLOB_NOSORT;

trait _RmDir
{
    public function rmdir(
        string $directory,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _RmDir;
            };
        }

        array_map(callback: static fn (string $file) => is_dir(filename: $file) ? $_helper->rmdir(directory: $file) : unlink(filename: $file), array: glob(pattern: $directory . '/*', flags: GLOB_NOSORT));

        return !is_dir(filename: $directory) || rmdir(directory: $directory);
    }
}
