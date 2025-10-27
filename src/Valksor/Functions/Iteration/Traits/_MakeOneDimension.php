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

use function ltrim;

use const PHP_INT_MAX;

trait _MakeOneDimension
{
    public function makeOneDimension(
        array $array,
        string $base = '',
        string $separator = '.',
        bool $onlyLast = false,
        int $depth = 0,
        int $maxDepth = PHP_INT_MAX,
        array $result = [],
        bool $allowList = false,
    ): array {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _IsAssociative;
                use _MakeOneDimension;
            };
        }

        if ($depth <= $maxDepth) {
            foreach ($array as $key => $value) {
                $key = ltrim(string: $base . '.' . $key, characters: '.');

                if ($_helper->isAssociative(array: $value, allowList: $allowList)) {
                    $result = $_helper->makeOneDimension(array: $value, base: $key, separator: $separator, onlyLast: $onlyLast, depth: $depth + 1, maxDepth: $maxDepth, result: $result, allowList: $allowList);

                    if ($onlyLast) {
                        continue;
                    }
                }

                $result[$key] = $value;
            }
        }

        return $result;
    }
}
