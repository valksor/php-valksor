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

namespace Valksor\Functions\Date\Traits;

use Valksor\Functions\Date\Functions;

use function gmdate;

trait _ExcelDate
{
    public function excelDate(
        int $timestamp,
        string $format = Functions::FORMAT,
    ): string {
        $base = 25569;

        if ($timestamp >= $base) {
            $unix = ($timestamp - $base) * 86400;
            $date = gmdate($format, $unix);

            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use _ValidateDateBasic;
                };
            }

            if ($_helper->validateDateBasic($date, $format)) {
                return $date;
            }
        }

        return (string) $timestamp;
    }
}
