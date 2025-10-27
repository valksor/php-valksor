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

namespace Valksor\Functions\Iteration\Traits;

use function array_key_exists;

trait _FirstMatchAsString
{
    public function firstMatchAsString(
        array $keys,
        array $haystack,
    ): ?string {
        foreach ($keys as $key) {
            if (array_key_exists($key, $haystack)) {
                return (string) $haystack[$key];
            }
        }

        return null;
    }
}
