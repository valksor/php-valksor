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

namespace Valksor\Functions\Text\Traits;

use Valksor\Functions\Preg;

use function mb_strtolower;

trait _SnakeCaseFromCamelCase
{
    public function snakeCaseFromCamelCase(
        string $string,
        string $separator = '_',
    ): string {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Preg\Traits\_Replace;
            };
        }

        return mb_strtolower(string: $_helper->replace(pattern: '#(?!^)[[:upper:]]+#', replacement: $separator . '$0', subject: $string));
    }
}
