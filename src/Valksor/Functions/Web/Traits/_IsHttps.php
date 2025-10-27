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

trait _IsHttps
{
    public function isHttps(
        Request $request,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _CheckHttps;
                use _CheckHttpXForwardedProto;
                use _CheckHttpXForwardedSsl;
                use _CheckServerPort;
            };
        }

        return $_helper->checkHttps(request: $request) || $_helper->checkServerPort(request: $request) || $_helper->checkHttpXForwardedSsl(request: $request) || $_helper->checkHttpXForwardedProto(request: $request);
    }
}
