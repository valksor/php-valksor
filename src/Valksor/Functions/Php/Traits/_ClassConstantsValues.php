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

use InvalidArgumentException;
use RuntimeException;

use function array_values;

trait _ClassConstantsValues
{
    /**
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function classConstantsValues(
        string $class,
    ): array {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use _ClassConstants;
            };
        }

        return array_values(array: $_helper->classConstants(class: $class));
    }
}
