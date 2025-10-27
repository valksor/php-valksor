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

use function is_numeric;
use function str_contains;

trait _NormalizedValue
{
    public function normalizedValue(
        string $value,
        string $delimiter = '.',
    ): string|int|float {
        if (is_numeric(value: $value)) {
            return str_contains(haystack: (string) $value, needle: $delimiter) ? (float) $value : (int) $value;
        }

        return $value;
    }
}
