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

use function array_is_list;
use function is_array;

trait _IsAssociative
{
    public function isAssociative(
        mixed $array,
        bool $allowList = false,
    ): bool {
        if (!is_array(value: $array) || [] === $array) {
            return false;
        }

        if ($allowList) {
            return true;
        }

        return !array_is_list(array: $array);
    }
}
