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

trait _IsPrimal
{
    public function isPrimal(
        int $number,
    ): bool {
        if ($number <= 1) {
            return false;
        }

        if ($number <= 3) {
            return true;
        }

        if (0 === $number % 2 || 0 === $number % 3) {
            return false;
        }

        for ($i = 5; $i * $i <= $number; $i += 6) {
            if (0 === $number % $i || 0 === $number % ($i + 2)) {
                return false;
            }
        }

        return true;
    }
}
