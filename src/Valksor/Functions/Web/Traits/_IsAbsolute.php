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

namespace Valksor\Functions\Web\Traits;

use Valksor\Functions\Preg;

trait _IsAbsolute
{
    public function isAbsolute(
        string $path,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Preg\Traits\_Match;
            };
        }

        return str_starts_with(haystack: $path, needle: '//') || $_helper->match(pattern: '#^[a-z-]{3,}://#i', subject: $path);
    }
}
