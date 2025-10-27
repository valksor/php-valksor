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

use Exception;
use InvalidArgumentException;
use ReflectionProperty;

use function sprintf;

trait _GetStatic
{
    /**
     * @throws InvalidArgumentException
     */
    public function getStatic(
        object $object,
        string $property,
        mixed ...$arguments,
    ): mixed {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _Return;
            };
        }

        try {
            if (new ReflectionProperty(class: $object, property: $property)->isStatic()) {
                return $_helper->return(fn () => $object::${$property}, $object, ...$arguments);
            }
        } catch (Exception) {
            // exception === unable to get object property
        }

        throw new InvalidArgumentException(message: sprintf('Property "%s" is not static', $property));
    }
}
