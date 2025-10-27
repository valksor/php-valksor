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

namespace Valksor\Functions\Date\Traits;

use DateTimeImmutable;
use DateTimeInterface;

trait _DateNullable
{
    public function dateNullable(
        ?string $dateString = null,
        ?string $format = null,
    ): ?DateTimeInterface {
        if (null === $dateString || null === $format || !$date = DateTimeImmutable::createFromFormat('!' . $format, $dateString)) {
            return null;
        }

        return $date;
    }
}
