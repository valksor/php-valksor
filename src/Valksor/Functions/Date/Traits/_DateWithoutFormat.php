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
use DateTimeInterface;
use Valksor\Functions\Date\Functions;
use Valksor\Functions\Php;

use function array_merge;

trait _DateWithoutFormat
{
    public function dateWithoutFormat(
        string $date,
        array $guesses = [],
    ): DateTimeInterface|string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Php\Traits\_ClassConstantsValues;
            };
        }

        $formats = array_merge($_helper->classConstantsValues(DateTimeImmutable::class), Functions::EXTRA_FORMATS, $guesses);

        foreach ($formats as $format) {
            $datetime = DateTimeImmutable::createFromFormat('!' . $format, $date);

            if ($datetime instanceof DateTimeInterface) {
                return $datetime;
            }
        }

        return $date;
    }
}
