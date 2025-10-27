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

use DateTimeImmutable;
use Valksor\Functions\Date\Functions;

trait _ValidateDateBasic
{
    public function validateDateBasic(
        mixed $date,
        string $format = Functions::FORMAT,
    ): bool {
        $object = DateTimeImmutable::createFromFormat('!' . $format, $date);

        return $object && $date === $object->format($format);
    }
}
