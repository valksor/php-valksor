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

namespace Valksor\Functions\Text\Traits;

use function str_contains;

trait _ContainsAny
{
    public function containsAny(
        string $haystack,
        array $needles = [],
    ): bool {
        foreach ($needles as $needle) {
            if (str_contains(haystack: $haystack, needle: (string) $needle)) {
                return true;
            }
        }

        return false;
    }
}
