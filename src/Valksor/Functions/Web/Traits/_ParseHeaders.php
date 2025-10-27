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
use function str_replace;

trait _ParseHeaders
{
    public function parseHeaders(
        string $rawHeaders = '',
    ): array {
        $headers = [];
        $headerArray = str_replace(search: '\\r', replace: '', subject: $rawHeaders);
        $headerArray = explode(separator: '\\n', string: $headerArray);

        foreach ($headerArray as $item) {
            $header = explode(separator: ': ', string: $item, limit: 2);

            if ($header[0] && !$header[1]) {
                $headers['status'] = $header[0];
            } elseif ($header[0] && $header[1]) {
                $headers[$header[0]] = $header[1];
            }
        }

        return $headers;
    }
}
