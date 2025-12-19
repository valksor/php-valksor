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

// PACKAGE: Always includes unicode/slashes unescaping and zero fraction preservation.

namespace Valksor\Functions\Iteration\Traits;

use function json_encode;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

trait _JsonEncode
{
    public function jsonEncode(
        mixed $value,
        int $flags = 0,
    ): string {
        $baseFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR;

        if ($flags & JSON_PRETTY_PRINT) {
            $baseFlags |= JSON_PRETTY_PRINT;
        }

        return json_encode(value: $value, flags: $baseFlags);
    }
}
