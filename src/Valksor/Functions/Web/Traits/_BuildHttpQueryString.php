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

use ReflectionException;

use function http_build_query;

trait _BuildHttpQueryString
{
    /**
     * @throws ReflectionException
     */
    public function buildHttpQueryString(
        object $object,
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _BuildHttpQueryString;
            };
        }

        return http_build_query(data: $_helper->buildHttpQueryArray(input: $object));
    }
}
