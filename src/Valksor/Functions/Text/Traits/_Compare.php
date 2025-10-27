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

use Valksor\Functions\Text\Functions;

use function mb_strlen;
use function mb_strpos;
use function mb_strtolower;
use function mb_substr;

trait _Compare
{
    public function compare(
        string $first,
        string $second,
        string $haystack = Functions::EN_LOWERCASE,
    ): int {
        $first = mb_strtolower(string: $first);
        $second = mb_strtolower(string: $second);
        $haystack = mb_strtolower(string: $haystack);

        for ($i = 0, $len = mb_strlen(string: $first); $i < $len; $i++) {
            if (($charFirst = mb_substr(string: $first, start: $i, length: 1)) === ($charSecond = mb_substr(string: $second, start: $i, length: 1))) {
                continue;
            }

            if ($i > mb_strlen(string: $second) || mb_strpos(haystack: $haystack, needle: $charFirst) > mb_strpos(haystack: $haystack, needle: $charSecond)) {
                return 1;
            }

            return -1;
        }

        return 0;
    }
}
