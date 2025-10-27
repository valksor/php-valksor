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

namespace Valksor\Functions\Iteration\Traits;

use function array_intersect_key;
use function array_keys;
use function is_array;

trait _ArrayIntersectKeyRecursive
{
    public function arrayIntersectKeyRecursive(
        array $first = [],
        array $second = [],
    ): array {
        $result = array_intersect_key($first, $second);

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _ArrayIntersectKeyRecursive;
            };
        }

        foreach (array_keys(array: $result) as $key) {
            if (is_array(value: $first[$key]) && is_array(value: $second[$key])) {
                $result[$key] = $_helper->arrayIntersectKeyRecursive(first: $first[$key], second: $second[$key]);
            }
        }

        return $result;
    }
}
