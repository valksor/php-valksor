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

use InvalidArgumentException;
use Valksor\Functions\Iteration;

use function count;
use function current;
use function usort;

trait _SortByParameter
{
    /**
     * @throws InvalidArgumentException
     */
    public function sortByParameter(
        array|object $data,
        string $parameter,
        string $order = 'ASC',
    ): object|array {
        if (count(value: $data) < 2) {
            return $data;
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _Usort;
                use Iteration\Traits\_IsSortable;
            };
        }

        $data = (array) $data;

        if (!$_helper->isSortable(item: current(array: $data), field: $parameter)) {
            throw new InvalidArgumentException(message: "Sorting parameter doesn't exist in sortable variable");
        }

        usort(array: $data, callback: $_helper->usort(parameter: $parameter, order: $order));

        return $data;
    }
}
