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

namespace Valksor\Functions\Iteration\Traits;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Valksor\Functions\Iteration\Exception\FieldNotFoundException;

use function is_object;
use function sprintf;

trait _Value
{
    public function value(
        array|object $objectOrArray,
        string|int $field,
        bool $throw = true,
    ): mixed {
        if (is_object(value: $objectOrArray)) {
            $result = PropertyAccess::createPropertyAccessor()->getValue(objectOrArray: $objectOrArray, propertyPath: $field);
        } else {
            $result = $objectOrArray[$field] ?? null;
        }

        if (null === $result && $throw) {
            throw new FieldNotFoundException(message: sprintf('Field "%s" does not exist', $field));
        }

        return $result;
    }
}
