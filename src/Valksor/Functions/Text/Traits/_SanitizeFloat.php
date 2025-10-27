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

namespace Valksor\Functions\Text\Traits;

use function filter_var;

use const FILTER_FLAG_ALLOW_FRACTION;
use const FILTER_SANITIZE_NUMBER_FLOAT;

trait _SanitizeFloat
{
    public function sanitizeFloat(
        string $string,
    ): float {
        return (float) filter_var(value: $string, filter: FILTER_SANITIZE_NUMBER_FLOAT, options: FILTER_FLAG_ALLOW_FRACTION);
    }
}
