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

namespace Valksor\Functions\Text\Traits;

use Valksor\Functions\Iteration;

use function is_array;

trait _ToString
{
    public function toString(
        mixed $value,
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _ScalarToString;
                use Iteration\Traits\_ArrayToString;
            };
        }

        return is_array($value) ? $_helper->arrayToString($value) : $_helper->scalarToString($value);
    }
}
