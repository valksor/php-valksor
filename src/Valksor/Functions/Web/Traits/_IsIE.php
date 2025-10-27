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

use Symfony\Component\HttpFoundation\Request;
use Valksor\Functions\Text;

trait _IsIE
{
    public function isIE(
        Request $request,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Text\Traits\_ContainsAny;
            };
        }

        return $_helper->containsAny(haystack: $request->server->get(key: 'HTTP_USER_AGENT'), needles: ['MSIE', 'Edge', 'Trident/7']);
    }
}
