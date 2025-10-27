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

namespace Valksor\Functions\Memoize;

use BackedEnum;

use function array_key_exists;

class Memoize
{
    protected array $cache = [];

    public function memoize(
        BackedEnum $context,
        int|string $key,
        callable $callback,
        bool $refresh = false,
        string ...$subKeys,
    ): mixed {
        $cache = &$this->cache[$context->value];
        $currentKey = $key;

        foreach ($subKeys as $subKey) {
            $cache[$currentKey] ??= [];
            $cache = &$cache[$currentKey];
            $currentKey = $subKey;
        }

        $cache ??= [];

        if ($refresh || !array_key_exists($currentKey, $cache)) {
            $cache[$currentKey] = $callback();
        }

        return $cache[$currentKey];
    }

    public function value(
        BackedEnum $context,
        int|string $key,
        mixed $default = null,
        string ...$subKeys,
    ): mixed {
        $cache = &$this->cache[$context->value];
        $cache ??= [];
        $currentKey = $key;

        foreach ($subKeys as $subKey) {
            if (!array_key_exists($currentKey, $cache)) {
                return $default;
            }

            $cache = &$cache[$currentKey];
            $currentKey = $subKey;
        }

        $cache ??= [];

        return $cache[$currentKey] ?? $default;
    }
}
