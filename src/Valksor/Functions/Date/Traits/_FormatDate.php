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
use Valksor\Functions\Date\Functions;

trait _FormatDate
{
    public function formatDate(
        string $string,
        string $format = Functions::FORMAT,
    ): string|bool {
        if (($date = DateTimeImmutable::createFromFormat('!' . $format, $string)) instanceof DateTimeImmutable) {
            return $date->format(Functions::FORMAT);
        }

        return false;
    }
}
