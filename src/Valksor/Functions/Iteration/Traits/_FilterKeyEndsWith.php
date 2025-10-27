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

use function array_filter;

use const ARRAY_FILTER_USE_KEY;

trait _FilterKeyEndsWith
{
    public function filterKeyEndsWith(
        array $input,
        string $endsWith,
    ): array {
        return array_filter(array: $input, callback: static fn ($key) => str_ends_with(haystack: $key, needle: $endsWith), mode: ARRAY_FILTER_USE_KEY);
    }
}
