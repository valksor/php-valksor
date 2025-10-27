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

use Valksor\Functions\Preg;

use function array_key_exists;
use function mb_strlen;
use function rtrim;

trait _LimitWords
{
    public function limitWords(
        string $text,
        int $limit = 100,
        string $append = '...',
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Preg\Traits\_Match;
            };
        }

        $_helper->match(pattern: '/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', subject: $text, matches: $matches);

        if (!array_key_exists(key: 0, array: $matches) || mb_strlen(string: $text) === mb_strlen(string: $matches[0])) {
            return $text;
        }

        return rtrim(string: $matches[0]) . $append;
    }
}
