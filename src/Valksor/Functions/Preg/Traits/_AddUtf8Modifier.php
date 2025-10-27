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

namespace Valksor\Functions\Preg\Traits;

use function is_array;

trait _AddUtf8Modifier
{
    public function addUtf8Modifier(
        array|string $pattern,
    ): array|string {
        $processPattern = static fn (string $pattern): string => $pattern . 'u';

        if (is_array($pattern)) {
            return array_map($processPattern, $pattern);
        }

        return $processPattern($pattern);
    }
}
