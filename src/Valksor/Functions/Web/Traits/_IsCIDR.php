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

use function count;
use function explode;
use function is_numeric;

trait _IsCIDR
{
    public function isCIDR(
        string $cidr,
    ): bool {
        $parts = explode(separator: '/', string: $cidr);

        if (2 === count(value: $parts) && is_numeric(value: $parts[1]) && 32 >= (int) $parts[1]) {
            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use _ValidateIPAddress;
                };
            }

            return $_helper->validateIPAddress(ipAddress: $parts[0], deny: false);
        }

        return false;
    }
}
