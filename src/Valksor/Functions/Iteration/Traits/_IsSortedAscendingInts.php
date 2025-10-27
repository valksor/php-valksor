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

use function count;
use function is_int;

trait _IsSortedAscendingInts
{
    public function isSortedAscendingInts(
        array $array,
    ): bool {
        $len = count($array);

        if (0 === $len) {
            return true;
        }

        if (!is_int($array[0])) {
            return false;
        }

        for ($i = 1; $i < $len; $i++) {
            if (!is_int($array[$i]) && $array[$i] < $array[$i - 1]) {
                return false;
            }
        }

        return true;
    }
}
