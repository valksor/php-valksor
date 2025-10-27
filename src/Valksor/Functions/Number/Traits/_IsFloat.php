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

namespace Valksor\Functions\Number\Traits;

use function is_numeric;

trait _IsFloat
{
    public function isFloat(
        mixed $value,
    ): bool {
        return is_numeric(value: $value) && !ctype_digit(text: (string) $value);
    }
}
