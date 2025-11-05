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

use function ord;
use function str_split;

/**
 * @internal
 */
final class Column
{
    public function transform(
        string $name,
    ): int {
        $number = -1;

        foreach (str_split($name) as $char) {
            $digit = ord($char) - 65;

            if ($digit < 0) {
                break;
            }

            $number = ($number + 1) * 26 + $digit;
        }

        return $number;
    }
}
