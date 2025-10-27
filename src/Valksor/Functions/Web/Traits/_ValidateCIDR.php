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

use function explode;
use function ip2long;

trait _ValidateCIDR
{
    public function validateCIDR(
        string $cidr,
    ): bool {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _CIDRRange;
                use _IsCIDR;
            };
        }

        if (!$_helper->isCIDR(cidr: $cidr)) {
            return false;
        }

        return (int) $_helper->CIDRRange(cidr: $cidr)[0] === ip2long(ip: explode(separator: '/', string: $cidr, limit: 2)[0]);
    }
}
