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

namespace Valksor\Bundle\Traits;

use ReflectionClass;
use ReflectionException;
use Valksor\Bundle\Constants\BundleContext;
use Valksor\Functions\Memoize\MemoizeCache;
use Valksor\Functions\Php;

use function is_object;

trait _LoadReflection
{
    /**
     * @return ReflectionClass<object>
     *
     * @throws ReflectionException
     */
    public function loadReflection(
        object|string $objectOrClass,
        MemoizeCache $memoize,
    ): ReflectionClass {
        $class = $objectOrClass;

        if (is_object($objectOrClass)) {
            $class = $objectOrClass::class;
        }

        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Php\Traits\_GetReflection;
            };
        }

        $reflection = $memoize->memoize(BundleContext::REFLECTION, $class, static fn () => $_helper->getReflection($objectOrClass));
        $memoize->memoize(BundleContext::REFLECTION, $reflection->getName(), static fn () => $reflection);

        return $reflection;
    }
}
