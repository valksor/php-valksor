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

namespace Valksor\Functions\Number\Traits;

trait _GreatestCommonDiviser
{
    public function greatestCommonDivisor(
        int $first,
        int $second,
    ): int {
        $first = abs($first);
        $second = abs($second);

        if (0 === $second) {
            return $first;
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _GreatestCommonDiviser;
            };
        }

        return $_helper->greatestCommonDivisor(first: $second, second: $first % $second);
    }
}
