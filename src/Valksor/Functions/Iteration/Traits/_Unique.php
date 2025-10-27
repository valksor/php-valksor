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

use function array_flip;
use function array_keys;
use function array_unique;

trait _Unique
{
    public function unique(
        array $input,
        bool $keepKeys = false,
    ): array {
        if ($keepKeys) {
            return array_unique(array: $input);
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _IsMultiDimentional;
            };
        }

        if ($_helper->isMultiDimensional(keys: $input)) {
            return $input;
        }

        return array_keys(array: array_flip(array: $input));
    }
}
