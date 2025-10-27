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

use function array_slice;
use function count;
use function round;

trait _MergeSort
{
    public function merge(
        array $left,
        array $right,
    ): array {
        $result = [];
        $i = $j = 0;

        $leftCount = count(value: $left);
        $rightCount = count(value: $right);

        while ($i < $leftCount && $j < $rightCount) {
            if ($left[$i] > $right[$j]) {
                $result[] = $right[$j];
                $j++;
            } else {
                $result[] = $left[$i];
                $i++;
            }
        }

        while ($i < $leftCount) {
            $result[] = $left[$i];
            $i++;
        }

        while ($j < $rightCount) {
            $result[] = $right[$j];
            $j++;
        }

        return $result;
    }

    public function mergeSort(
        array $array,
    ): array {
        if (1 >= count(value: $array)) {
            return $array;
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _MergeSort;
            };
        }

        $middle = (int) round(num: count(value: $array) / 2);
        $left = array_slice(array: $array, offset: 0, length: $middle);
        $right = array_slice(array: $array, offset: $middle);

        $left = $_helper->mergeSort(array: $left);
        $right = $_helper->mergeSort(array: $right);

        return $_helper->merge(left: $left, right: $right);
    }
}
