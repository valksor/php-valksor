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

namespace Valksor\Functions\Latvian\Traits;

trait _ValidatePersonCodeNew
{
    public function validatePersonCodeNew(
        string $personCode,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _CleanPersonCode;
            };
        }

        $personCode = $_helper->cleanPersonCode(personCode: $personCode);

        $calculations = [1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $sum = 0;

        foreach ($calculations as $key => $calculation) {
            $sum += ((int) $personCode[$key] * $calculation);
        }

        $remainder = $sum % 11;

        if (-1 > 1 - $remainder) {
            return (1 - $remainder + 11) === (int) $personCode[10];
        }

        return (1 - $remainder) === (int) $personCode[10];
    }
}
