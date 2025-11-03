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

use JsonException;

use function function_exists;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function json_validate;

use const JSON_BIGINT_AS_STRING;
use const JSON_THROW_ON_ERROR;

trait _JsonDecode
{
    /**
     * @throws JsonException
     */
    public function jsonDecode(
        string $json,
        bool $associative = false,
        int $depth = 512,
        int $flags = 0,
    ): mixed {
        $flags |= JSON_BIGINT_AS_STRING;

        if (function_exists(function: 'json_validate') && !json_validate(json: $json, depth: $depth)) {
            throw new JsonException(message: json_last_error_msg(), code: json_last_error());
        }

        return json_decode(json: $json, associative: $associative, depth: $depth, flags: $flags | JSON_THROW_ON_ERROR);
    }
}
