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

use function is_array;

trait _IsMultiDimentional
{
    public function isMultiDimensional(
        array $keys = [],
    ): bool {
        foreach ($keys as $key) {
            if (is_array(value: $key)) {
                return true;
            }
        }

        return false;
    }
}
