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

namespace Valksor\Functions\Sort\Traits;

use Closure;
use Valksor\Functions\Php;

trait _Usort
{
    public function usort(
        string $parameter,
        string $order,
    ): Closure {
        return static function (array|object $first, array|object $second) use ($parameter, $order): int {
            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use Php\Traits\_Parameter;
                };
            }

            if (($firstSort = $_helper->parameter(variable: $first, key: $parameter)) === ($secondSort = $_helper->parameter(variable: $second, key: $parameter))) {
                return 0;
            }

            $flip = 'DESC' === $order ? -1 : 1;

            if ($firstSort > $secondSort) {
                return $flip;
            }

            return -1 * $flip;
        };
    }
}
