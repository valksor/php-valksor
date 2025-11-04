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

use DateTime;

trait _CalculateEaster
{
    public function calculateEaster(
        int $year,
    ): DateTime {
        // Western Easter calculation (Gregorian calendar) using Anonymous Gregorian algorithm
        // Also known as the Meeus/Jones/Butcher algorithm for calculating Easter Sunday

        // Golden Number: year's position in the 19-year Metonic lunar cycle
        $goldenNumber = $year % 19;

        // Century calculations for Gregorian calendar corrections
        $century = floor($year / 100);
        $yearOfCentury = $year % 100;

        // Leap day corrections for centuries (Gregorian calendar omits leap days in certain centuries)
        $centuryLeapDays = floor($century / 4);
        $centuryLeapRemainder = $century % 4;

        // Epact (moon's age) correction factors for calendar synchronization
        $epactCorrectionFactor = floor(($century + 8) / 25);
        $additionalEpactCorrection = floor(($century - $epactCorrectionFactor + 1) / 3);

        // Epact: age of the moon on January 1st (in days), crucial for Easter calculation
        $epact = (19 * $goldenNumber + $century - $centuryLeapDays - $additionalEpactCorrection + 15) % 30;

        // Leap day calculations within the century
        $leapDays = floor($yearOfCentury / 4);
        $leapRemainder = $yearOfCentury % 4;

        // Day of week adjustment to ensure Easter falls on Sunday
        $dayOfWeekAdjustment = (32 + 2 * $centuryLeapRemainder + 2 * $leapDays - $epact - $leapRemainder) % 7;

        // Additional century correction for algorithm precision
        $centuryCorrection = floor(($goldenNumber + 11 * $epact + 22 * $dayOfWeekAdjustment) / 451);

        // Calculate Easter month and day from the computed values
        $month = floor(($epact + $dayOfWeekAdjustment - 7 * $centuryCorrection + 114) / 31);
        $day = (($epact + $dayOfWeekAdjustment - 7 * $centuryCorrection + 114) % 31) + 1;

        return new DateTime("$year-$month-$day");
    }
}
