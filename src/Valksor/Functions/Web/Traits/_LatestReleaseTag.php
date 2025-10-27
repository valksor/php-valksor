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

use Valksor\Functions\Iteration;
use Valksor\Functions\Local;

use function sprintf;

trait _LatestReleaseTag
{
    public function latestReleaseTag(
        string $repository,
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Iteration\Traits\_JsonDecode;
                use Local\Traits\_CurlUA;
            };
        }

        $options = [
            'http' => [
                'header' => sprintf("User-Agent: %s\r\n", $_helper->getCurlUserAgent()),
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents(sprintf('https://api.github.com/repos/%s/releases/latest', $repository), false, $context);

        return $_helper->jsonDecode((string) $response, 1)['tag_name'];
    }
}
