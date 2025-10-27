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

use function in_array;
use function is_bool;
use function strtolower;

trait _IsBool
{
    public function isBool(
        mixed $value,
    ): bool {
        if (is_bool(value: $value)) {
            return true;
        }

        return in_array(strtolower(string: (string) $value), ['y', 'true', 'n', 'false'], true);
    }
}
