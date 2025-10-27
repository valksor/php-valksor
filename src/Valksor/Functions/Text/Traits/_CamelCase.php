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

namespace Valksor\Functions\Text\Traits;

use Valksor\Functions\Preg;

use function lcfirst;
use function mb_strtolower;
use function str_replace;
use function ucwords;

trait _CamelCase
{
    public function camelCase(
        string $string,
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Preg\Traits\_Replace;
            };
        }

        return $_helper->replace(pattern: '#\s+#', replacement: '', subject: lcfirst(string: ucwords(string: mb_strtolower(string: str_replace(search: '_', replace: ' ', subject: $string)))));
    }
}
