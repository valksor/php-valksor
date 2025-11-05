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

/**
 * @internal
 */
final class Date
{
    private const string DATETIME_FORMAT = 'd-m-Y H:i:s';

    public function transform(
        float|int $value,
    ): DateTimeImmutable {
        $value = (int) floor($value);

        /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
        $unix = ($value - 25569) * 86400;
        $date = gmdate(self::DATETIME_FORMAT, $unix);

        return date_create_immutable_from_format('!' . self::DATETIME_FORMAT, $date);
    }
}
