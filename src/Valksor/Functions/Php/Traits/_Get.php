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
use ReflectionException;
use ReflectionProperty;

use function sprintf;

trait _Get
{
    /**
     * @throws InvalidArgumentException
     */
    public function get(
        object $object,
        string $property,
        bool $throwOnUnInitialized = false,
        mixed ...$arguments,
    ): mixed {
        try {
            $reflectionProperty = (new ReflectionProperty(class: $object, property: $property));
        } catch (ReflectionException) {
            throw new InvalidArgumentException(message: sprintf('Unable to get property "%s" of object %s', $property, $object::class));
        }

        if (!$reflectionProperty->isInitialized(object: $object)) {
            if ($throwOnUnInitialized) {
                throw new InvalidArgumentException(message: sprintf('%s::%s must not be accessed before initialization', $object::class, $property));
            }

            return null;
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _GetNonStatic;
                use _GetStatic;
            };
        }

        try {
            return $_helper->getNonStatic($object, $property, ...$arguments);
        } catch (Exception) {
            // exception === unable to get object property
        }

        return $_helper->getStatic($object, $property, ...$arguments);
    }
}
