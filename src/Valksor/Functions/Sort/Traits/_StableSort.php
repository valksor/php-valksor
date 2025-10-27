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

namespace Valksor\Functions\Sort\Traits;

use function array_map;
use function array_walk;
use function usort;

trait _StableSort
{
    public function stableSort(
        array $elements,
        callable $getComparedValue,
        callable $compareValues,
    ): array {
        array_walk($elements, static function (&$element, int $index) use ($getComparedValue): void {
            $element = [$element, $index, $getComparedValue($element)];
        });

        usort($elements, static function ($a, $b) use ($compareValues): int {
            $comparison = $compareValues($a[2], $b[2]);

            if (0 !== $comparison) {
                return $comparison;
            }

            return $a[1] <=> $b[1];
        });

        return array_map(static fn (array $item) => $item[0], $elements);
    }
}
