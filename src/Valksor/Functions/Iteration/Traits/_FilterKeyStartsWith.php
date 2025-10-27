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

use function array_filter;

use const ARRAY_FILTER_USE_KEY;

trait _FilterKeyStartsWith
{
    public function filterKeyStartsWith(
        array $input,
        string $startsWith,
    ): array {
        return array_filter(array: $input, callback: static fn ($key) => str_starts_with(haystack: (string) $key, needle: $startsWith), mode: ARRAY_FILTER_USE_KEY);
    }
}
