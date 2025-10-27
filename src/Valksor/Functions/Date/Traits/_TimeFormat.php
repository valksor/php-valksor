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

use Valksor\Functions\Date\Functions;

use function floor;
use function round;
use function trim;

trait _TimeFormat
{
    public function format(
        int|float $timestamp,
        bool $asArray = false,
    ): array|string {
        $timestamp = round($timestamp * 1000);
        $result = $asArray ? [] : '';

        foreach (Functions::TIME as $unit => $value) {
            if ($timestamp >= $value) {
                $time = (int) floor($timestamp / $value);

                if ($time > 0) {
                    match ($asArray) {
                        true => $result[$unit] = $time,
                        false => $result .= $time . ' ' . $unit . (1 === $time ? '' : 's') . ' ',
                    };
                }

                $timestamp -= $time * $value;
            }
        }

        return match ($asArray) {
            true => $result,
            false => trim($result),
        };
    }
}
