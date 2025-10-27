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

use Valksor\Functions\Text;

use function substr;

trait _ValidateDate
{
    public function validateDate(
        string $date,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Text\Traits\_KeepNumeric;
            };
        }

        $date = $_helper->keepNumeric($date);
        $day = (int) substr($date, 0, 2);
        $month = (int) substr($date, 2, 2);

        if (1 > $month || 12 < $month) {
            return false;
        }

        $daysInMonth = [
            31,
            28,
            31,
            30,
            31,
            30,
            31,
            31,
            30,
            31,
            30,
            31,
        ];

        if (0 === (int) substr($date, 4, 2) % 4) {
            $daysInMonth[1] = 29;
        }

        return 0 < $day && $daysInMonth[$month - 1] >= $day;
    }
}
