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

abstract class AbstractHandler implements Handler
{
    private ?Handler $handler = null;

    public function handle(
        ...$arguments,
    ): mixed {
        return $this->handler?->handle(...$arguments);
    }

    public function next(
        Handler $handler,
    ): Handler {
        $this->handler = $handler;

        return $this;
    }
}
