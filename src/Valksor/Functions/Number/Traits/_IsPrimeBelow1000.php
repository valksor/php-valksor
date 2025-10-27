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

trait _IsPrimeBelow1000
{
    public function isPrimeBelow1000(
        int $number,
    ): ?bool {
        if (1000 <= $number) {
            return null;
        }

        for ($x = 2; $x < $number; $x++) {
            if (0 === $number % $x) {
                return false;
            }
        }

        return 1 !== $number;
    }
}
