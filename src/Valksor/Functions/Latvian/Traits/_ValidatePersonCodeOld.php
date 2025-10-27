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

use function floor;
use function substr;

trait _ValidatePersonCodeOld
{
    public function validatePersonCodeOld(
        string $personCode,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _CleanPersonCode;
            };
        }

        $personCode = $_helper->cleanPersonCode(personCode: $personCode);

        $check = '01060307091005080402';
        $checksum = 1;

        for ($i = 0; $i < 10; $i++) {
            $checksum -= (int) $personCode[$i] * (int) substr(string: $check, offset: $i * 2, length: 2);
        }

        return (int) ($checksum - floor(num: $checksum / 11) * 11) === (int) $personCode[10];
    }
}
