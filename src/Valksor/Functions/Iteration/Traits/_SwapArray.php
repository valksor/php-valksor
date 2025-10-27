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

trait _SwapArray
{
    public function swapArray(
        array &$array,
        mixed $foo,
        mixed $bar,
    ): void {
        if ($array[$foo] === $array[$bar]) {
            return;
        }

        [$array[$foo], $array[$bar]] = [$array[$bar], $array[$foo]];
    }
}
