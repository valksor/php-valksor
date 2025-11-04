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

use DateMalformedStringException;
use DateTime;

trait _CalculateMothersDay
{
    /**
     * @throws DateMalformedStringException
     */
    public function calculateMothersDay(
        int $year,
    ): DateTime {
        // Second Sunday of May
        $date = new DateTime("$year-05-01");

        // Find first Sunday
        while ('0' !== $date->format('w')) {
            $date->modify('+1 day');
        }

        // Add 7 days to get second Sunday
        $date->modify('+7 days');

        return $date;
    }
}
