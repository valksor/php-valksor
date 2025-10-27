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

use function str_replace;

trait _Nl2br
{
    public function nl2br(
        string $string,
    ): string {
        return str_replace(["\r\n", "\r", "\n"], '<br />', $string);
    }
}
