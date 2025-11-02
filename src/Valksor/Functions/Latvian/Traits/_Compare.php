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

namespace Valksor\Functions\Latvian\Traits;

use Valksor\Functions\Iteration;
use Valksor\Functions\Text;

trait _Compare
{
    public function compare(
        array|object $first,
        array|object $second,
        string|int $field,
    ): int {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Iteration\Traits\_Value;
                use Text\Traits\_Compare;
            };
        }

        return $_helper->compare(first: $_helper->value(objectOrArray: $first, field: $field), second: $_helper->value(objectOrArray: $second, field: $field), haystack: Text\Functions::LV_LOWERCASE);
    }
}
