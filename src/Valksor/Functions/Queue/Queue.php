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

namespace Valksor\Functions\Queue;

use Countable;

use function array_shift;
use function count;
use function current;
use function in_array;

/**
 * @template T
 */
final class Queue implements Countable
{
    public function __construct(
        /** @var array<T> */
        private array $items = [],
    ) {
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function contains(
        mixed $item,
    ): bool {
        return in_array($item, $this->items, true);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    public function peek(): mixed
    {
        return current($this->items);
    }

    public function pop(): mixed
    {
        if ($this->isEmpty()) {
            return false;
        }

        return array_shift($this->items);
    }

    public function push(
        mixed $item,
    ): void {
        if (null !== $item) {
            $this->items[] = $item;
        }
    }
}
