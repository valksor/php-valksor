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

namespace Valksor\Functions\Php\Traits;

trait _VoidObject
{
    public function voidObject(
        object $object,
        string $function,
        mixed ...$arguments,
    ): void {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _Void;
            };
        }

        $_helper->void(fn () => $object->{$function}(...$arguments), $object, ...$arguments);
    }
}
