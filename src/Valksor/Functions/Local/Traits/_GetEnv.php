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

namespace Valksor\Functions\Local\Traits;

use function getenv;

trait _GetEnv
{
    public function getenv(
        string $name,
        bool $localOnly = true,
    ): mixed {
        return getenv(name: $name, local_only: $localOnly) ?: ($_ENV[$name] ?? $name);
    }
}
