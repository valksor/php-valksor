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

use function is_array;

trait _IsEmpty
{
    public function isEmpty(
        mixed $variable,
        bool $result = true,
    ): bool {
        if (is_array(value: $variable) && [] !== $variable) {
            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use _IsEmpty;
                };
            }

            foreach ($variable as $item) {
                $result = $_helper->isEmpty(variable: $item, result: $result);
            }

            return $result;
        }

        return empty($variable);
    }
}
