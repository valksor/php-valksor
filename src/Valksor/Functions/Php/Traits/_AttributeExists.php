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

use ReflectionMethod;

trait _AttributeExists
{
    public function attributeExists(
        ReflectionMethod $reflectionMethod,
        string $filterClass,
    ): bool {
        return [] !== $reflectionMethod->getAttributes(name: $filterClass);
    }
}
