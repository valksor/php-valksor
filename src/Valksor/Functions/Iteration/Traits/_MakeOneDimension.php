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

use Generator;

use function ltrim;

use const PHP_INT_MAX;

trait _MakeOneDimension
{
    /**
     * @param array  $array     The multidimensional array to flatten
     * @param string $base      The base key path for nested elements
     * @param string $separator The separator between nested keys
     * @param bool   $onlyLast  If true, only yields the deepest nested values
     * @param int    $depth     Current recursion depth (for internal use)
     * @param int    $maxDepth  Maximum recursion depth
     * @param bool   $allowList Whether to treat indexed arrays as associative
     *
     * @return Generator<string, mixed> Yields key-value pairs
     */
    public function makeOneDimension(
        array $array,
        string $base = '',
        string $separator = '.',
        bool $onlyLast = false,
        int $depth = 0,
        int $maxDepth = PHP_INT_MAX,
        bool $allowList = false,
    ): Generator {
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
                    yield from $_helper->makeOneDimension(
                        array: $value,
                        base: $key,
                        separator: $separator,
                        onlyLast: $onlyLast,
                        depth: $depth + 1,
                        maxDepth: $maxDepth,
                        allowList: $allowList,
                    );

                    if ($onlyLast) {
                        continue;
                    }
                }

                yield $key => $value;
            }
        }
    }
}
