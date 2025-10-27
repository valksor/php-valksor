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

use Symfony\Component\String\Inflector\EnglishInflector;

use function end;

trait _Singularize
{
    public function singularize(
        string $string,
    ): string {
        static $_helper = null;
        static $_inflector = null;

        if (null === $_helper) {
            $_helper = new class {
                use _PascalCase;
                use _SnakeCaseFromCamelCase;
            };
        }

        if (null === $_inflector) {
            $_inflector = new EnglishInflector();
        }

        $singular = $_inflector->singularize($_helper->pascalCase($string));

        return $_helper->snakeCaseFromCamelCase(end($singular));
    }
}
