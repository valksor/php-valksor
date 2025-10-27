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

use function array_intersect;

trait _HaveCommonElements
{
    public function haveCommonElements(
        array $array1,
        array $array2,
    ): bool {
        return [] !== array_intersect($array1, $array2);
    }
}
