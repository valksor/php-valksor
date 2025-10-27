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

use function array_map;
use function explode;
use function long2ip;

trait _CIDRRange
{
    public function CIDRRange(
        string $cidr,
        bool $int = true,
    ): array {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _IsCIDR;
            };
        }

        if (!$_helper->isCIDR(cidr: $cidr)) {
            return ['0', '0'];
        }

        [$base, $bits,] = explode(separator: '/', string: $cidr);
        $bits = (int) $bits;
        [$part1, $part2, $part3, $part4,] = array_map('intval', explode(separator: '.', string: $base));
        $sum = ($part1 << 24) + ($part2 << 16) + ($part3 << 8) + $part4;
        $mask = (0 === $bits) ? 0 : (~0 << (32 - $bits));

        $low = $sum & $mask;
        $high = $sum | (~$mask & 0xFFFFFFFF);

        if ($int) {
            return [(string) $low, (string) $high];
        }

        return [long2ip(ip: $low), long2ip(ip: $high)];
    }
}
