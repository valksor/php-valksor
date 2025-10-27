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

use function array_diff;

trait _RemoveFromArray
{
    public function removeFromArray(
        array &$input,
        mixed $value,
    ): void {
        $input = array_diff($input, [$value]);
    }
}
