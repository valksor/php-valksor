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

namespace Valksor\Functions\Preg\Traits;

use function is_array;
use function str_replace;
use function strrpos;
use function substr;

trait _RemoveUtf8Modifier
{
    public function removeUtf8Modifier(
        array|string $pattern,
    ): array|string {
        if ('' === $pattern || [] === $pattern) {
            return '';
        }

        $processPattern = static function (string $pattern): string {
            $endDelimiterPosition = strrpos($pattern, $pattern[0]);

            return substr($pattern, 0, $endDelimiterPosition) . str_replace('u', '', substr($pattern, $endDelimiterPosition));
        };

        if (is_array($pattern)) {
            return array_map($processPattern, $pattern);
        }

        return $processPattern($pattern);
    }
}
