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

namespace Valksor\Functions\Php\Traits;

use function is_array;

trait _Parameter
{
    public function parameter(
        array|object $variable,
        mixed $key,
    ): mixed {
        if (is_array(value: $variable)) {
            return $variable[$key];
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _Get;
            };
        }

        return $_helper->get(object: $variable, property: $key);
    }
}
