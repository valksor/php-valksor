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

namespace Valksor\Functions\Web\Traits;

use Valksor\Functions\Preg;

use function array_combine;
use function array_keys;
use function array_map;
use function bin2hex;
use function parse_str;
use function urldecode;

trait _ArrayFromQueryString
{
    public function arrayFromQueryString(
        string $query,
    ): array {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Preg\Traits\_ReplaceCallback;
            };
        }

        parse_str(string: $_helper->replaceCallback(pattern: '#(?:^|(?<=&))[^=[]+#', callback: static fn ($match) => bin2hex(string: urldecode(string: $match[0])), subject: $query), result: $values);

        return array_combine(keys: array_map(callback: 'hex2bin', array: array_keys(array: $values)), values: $values);
    }
}
