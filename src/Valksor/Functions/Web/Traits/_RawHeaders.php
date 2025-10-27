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

use Symfony\Component\HttpFoundation\HeaderBag;

trait _RawHeaders
{
    public function rawHeaders(
        HeaderBag $headerBag,
    ): string {
        $string = '';

        foreach ($headerBag->all() as $header => $value) {
            $string .= $header . ': ' . $value[0] . '\r\n';
        }

        return $string;
    }
}
