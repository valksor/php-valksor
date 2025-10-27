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
use Valksor\Functions\Iteration;
use Valksor\Functions\Web\Functions;

trait _RemoteIp
{
    public function remoteIp(
        Request $request,
        bool $trust = false,
    ): string {
        $headers = [Functions::REMOTE_ADDR, ];

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Iteration\Traits\_FirstMatchAsString;
            };
        }

        if ($trust) {
            $headers = [Functions::HTTP_CLIENT_IP, Functions::HTTP_X_REAL_IP, Functions::HTTP_X_FORWARDED_FOR, Functions::REMOTE_ADDR, ];
        }

        return (string) $_helper->firstMatchAsString(keys: $headers, haystack: $request->server->all());
    }
}
