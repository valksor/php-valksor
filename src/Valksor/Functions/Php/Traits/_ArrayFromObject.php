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

use ReflectionObject;

trait _ArrayFromObject
{
    public function arrayFromObject(
        object $object,
    ): array {
        $input = [];

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _Get;
            };
        }

        foreach (new ReflectionObject(object: $object)->getProperties() as $reflectionProperty) {
            $input[$name = $reflectionProperty->getName()] = $_helper->get(object: $object, property: $name);
        }

        return $input;
    }
}
