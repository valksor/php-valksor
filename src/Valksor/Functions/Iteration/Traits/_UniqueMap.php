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

namespace Valksor\Functions\Iteration\Traits;

use function array_map;
use function array_unique;

trait _UniqueMap
{
    public function uniqueMap(
        array &$array,
    ): void {
        $array = array_map(callback: 'unserialize', array: array_unique(array: array_map(callback: 'serialize', array: $array)));
    }
}
