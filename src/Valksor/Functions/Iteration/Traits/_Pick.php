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

use Valksor\Functions\Php;

trait _Pick
{
    public function pick(
        array $array,
    ): int|string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Php\Traits\_Randomizer;
            };
        }

        return $_helper->randomizer()->pickArrayKeys(array: $array, num: 1)[0];
    }
}
