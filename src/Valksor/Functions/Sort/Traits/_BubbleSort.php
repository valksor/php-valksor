<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Dāvis Zālītis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Functions\Sort\Traits;

use Valksor\Functions\Iteration;

use function count;

trait _BubbleSort
{
    public function bubbleSort(
        array &$array,
    ): void {
        $count = count(value: $array);
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Iteration\Traits\_SwapArray;
            };
        }

        for ($foo = 0; $foo < $count; $foo++) {
            for ($bar = 0; $bar < $count - 1; $bar++) {
                if ($bar < $count && $array[$bar] > $array[$bar + 1]) {
                    $_helper->swapArray(array: $array, foo: $bar, bar: $bar + 1);
                }
            }
        }
    }
}
