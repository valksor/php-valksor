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

namespace Valksor\Functions\Php\Traits;

use Exception;
use InvalidArgumentException;
use ReflectionProperty;

use function sprintf;

trait _GetNonStatic
{
    /**
     * @throws InvalidArgumentException
     */
    public function getNonStatic(
        object $object,
        string $property,
        mixed ...$arguments,
    ): mixed {
        try {
            if (new ReflectionProperty(class: $object, property: $property)->isStatic()) {
                throw new InvalidArgumentException(message: 'non static property');
            }
        } catch (Exception) {
            throw new InvalidArgumentException(message: sprintf('Property "%s" is static', $property));
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _Return;
            };
        }

        return $_helper->return(fn () => $object->{$property}, $object, ...$arguments);
    }
}
