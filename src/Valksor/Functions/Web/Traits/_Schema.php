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
use Valksor\Functions\Web\Functions;

trait _Schema
{
    public function schema(
        Request $request,
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _IsHttps;
            };
        }

        return $_helper->isHttps(request: $request) ? Functions::SCHEMA_HTTPS : Functions::SCHEMA_HTTP;
    }
}
