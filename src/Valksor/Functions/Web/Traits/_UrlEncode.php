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

use function http_build_query;
use function parse_url;

trait _UrlEncode
{
    public function urlEncode(
        string $url,
    ): string {
        $urlParsed = parse_url(url: $url);

        $port = (string) ($urlParsed['port'] ?? '');
        $query = $urlParsed['query'] ?? '';

        if ('' !== $query) {
            static $_helper = null;

            if (null === $_helper) {
                $_helper = new class {
                    use _ArrayFromQueryString;
                };
            }

            /** @var string $query */
            $query = '?' . http_build_query(data: $_helper->arrayFromQueryString(query: $query));
        }

        if ($port && ':' !== $port[0]) {
            $port = ':' . $port;
        }

        return $urlParsed['scheme'] . '://' . $urlParsed['host'] . $port . ($urlParsed['path'] ?? '') . $query;
    }
}
