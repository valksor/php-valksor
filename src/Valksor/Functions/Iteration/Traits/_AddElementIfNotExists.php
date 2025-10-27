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

use function array_key_exists;
use function in_array;

trait _AddElementIfNotExists
{
    public function addElementIfNotExists(
        ?array &$array,
        mixed $element,
        mixed $key = null,
    ): void {
        $array ??= [];

        if ((null !== $key) && !array_key_exists($key, $array)) {
            $array[$key] = $element;

            return;
        }

        if (!in_array(needle: $element, haystack: $array, strict: true)) {
            $array[$key] = $element;
        }
    }
}
