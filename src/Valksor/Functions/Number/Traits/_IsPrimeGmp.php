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

use function function_exists;
use function gmp_prob_prime;

trait _IsPrimeGmp
{
    public function isPrimeGmp(
        int $number,
        bool $override = false,
    ): ?bool {
        if (!$override && function_exists(function: 'gmp_prob_prime')) {
            return match (gmp_prob_prime(num: (string) $number)) {
                0 => false,
                2 => true,
                default => null,
            };
        }

        return null;
    }
}
