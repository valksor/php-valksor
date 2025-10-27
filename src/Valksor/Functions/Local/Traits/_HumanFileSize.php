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

namespace Valksor\Functions\Local\Traits;

use function floor;
use function sprintf;
use function strlen;

trait _HumanFileSize
{
    public function humanFileSize(
        int $bytes,
        int $decimals = 2,
    ): string {
        $units = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
        $bytesAsString = (string) $bytes;
        $factor = (int) floor(num: (strlen(string: $bytesAsString) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytesAsString / (1024 ** $factor)) . $units[$factor];
    }
}
