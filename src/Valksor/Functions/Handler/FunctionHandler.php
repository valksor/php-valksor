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

namespace Valksor\Functions\Handler;

use Valksor\Functions\Php;

use function is_object;

class FunctionHandler extends AbstractHandler
{
    public function __construct(
        private readonly string $function,
        private readonly ?object $instance = null,
    ) {
    }

    public function handle(
        ...$arguments,
    ): mixed {
        static $_helper = null;

        if (null === $_helper) {
            $_helper = new class {
                use Php\Traits\_ReturnFunction;
                use Php\Traits\_ReturnObject;
            };
        }

        $result = !is_object(value: $this->instance)
            ? $_helper->returnFunction($this->function, ...$arguments)
            : $_helper->returnObject($this->instance, $this->function, ...$arguments);

        return parent::handle(...$arguments) ?? $result;
    }
}
