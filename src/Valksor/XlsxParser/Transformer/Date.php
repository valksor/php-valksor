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

namespace Valksor\XlsxParser\Transformer;

use DateTimeImmutable;

use function date_create_immutable_from_format;
use function floor;
use function gmdate;
use function round;
use function sprintf;

/**
 * @internal
 */
final class Date
{
    private const string DATETIME_FORMAT = 'd-m-Y H:i:s';

    public function transform(
        float|int $value,
    ): DateTimeImmutable {
        // Handle pure time values (value < 1)
        if ($value < 1) {
            $totalSeconds = (int) round($value * 86400); // Convert fractional day to total seconds
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);

            // Create a DateTimeImmutable with only the time component
            return new DateTimeImmutable(sprintf('%02d:%02d:00', $hours, $minutes));
        }

        // Default/Standard: Handle full date or date-time values (value >= 1)
        // Convert Excel date to UNIX timestamp
        $unix = (int) (($value - 25569) * 86400);
        $date = gmdate(self::DATETIME_FORMAT, $unix);

        return date_create_immutable_from_format('!' . self::DATETIME_FORMAT, $date);
    }
}
