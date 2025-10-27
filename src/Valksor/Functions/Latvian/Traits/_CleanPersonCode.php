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

namespace Valksor\Functions\Latvian\Traits;

use InvalidArgumentException;
use Valksor\Functions\Text;

use function sprintf;
use function str_replace;
use function strlen;

trait _CleanPersonCode
{
    public function cleanPersonCode(
        string $personCode,
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Text\Traits\_KeepNumeric;
            };
        }

        $personCode = $_helper->keepNumeric(text: $personCode);
        $personCode = (string) str_replace(search: '-', replace: '', subject: $personCode);

        if (11 !== strlen(string: $personCode)) {
            throw new InvalidArgumentException(message: sprintf('Invalid person code: "%s"', $personCode));
        }

        return $personCode;
    }
}
