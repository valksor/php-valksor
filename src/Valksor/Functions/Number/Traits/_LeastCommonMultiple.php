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

namespace Valksor\Functions\Number\Traits;

trait _LeastCommonMultiple
{
    public function leastCommonMultiple(
        int $first,
        int $second,
    ): int {
        if (0 === $first || 0 === $second) {
            return 0;
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _GreatestCommonDiviser;
            };
        }

        return (int) abs($first * $second / $_helper->greatestCommonDivisor(first: $first, second: $second));
    }
}
