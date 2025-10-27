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

namespace Valksor\Functions\Iteration\Traits;

use Valksor\Functions\Local;
use Valksor\Functions\Php;

use function array_rand;

trait _Pick
{
    public function pick(
        array $array,
    ): int|string|array {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Local\Traits\_IsInstalled;
                use Php\Traits\_Randomizer;
            };
        }

        if ($_helper->isInstalled(packages: ['random'])) {
            return $_helper->randomizer()->pickArrayKeys(array: $array, num: 1)[0];
        }

        return array_rand(array: $array);
    }
}
