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

// PACKAGE: Provides iteration, array access, property access, and method delegation.

namespace Valksor\Functions\Iteration;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;

use function count;
use function is_callable;
use function ucfirst;

/**
 * Lazy-loading wrapper that memoizes results from a callable producer.
 * Supports iteration, array access, property access, and method delegation.
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements IteratorAggregate<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 */
final class Lazy implements IteratorAggregate, ArrayAccess, Countable
{
    /** @var array<TKey, TValue>|null */
    private ?array $memo = null;

    /** @var callable():array<TKey, TValue> */
    private $producer;

    /**
     * @param callable():array<TKey, TValue> $producer
     */
    public function __construct(
        callable $producer,
    ) {
        $this->producer = $producer;
    }

    /**
     * @param array<mixed> $arguments
     */
    public function __call(
        string $name,
        array $arguments,
    ): mixed {
        $resolved = $this->resolve();

        if (isset($resolved[$name]) && is_callable($resolved[$name])) {
            return $resolved[$name](...$arguments);
        }

        $getter = 'get' . ucfirst($name);

        if (isset($resolved[$getter]) && is_callable($resolved[$getter])) {
            return $resolved[$getter](...$arguments);
        }

        $isser = 'is' . ucfirst($name);

        if (isset($resolved[$isser]) && is_callable($resolved[$isser])) {
            return $resolved[$isser](...$arguments);
        }

        $hasser = 'has' . ucfirst($name);

        if (isset($resolved[$hasser]) && is_callable($resolved[$hasser])) {
            return $resolved[$hasser](...$arguments);
        }

        return null;
    }

    public function count(): int
    {
        return count($this->resolve());
    }

    /**
     * @return Traversable<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        yield from $this->resolve();
    }

    public function offsetExists(
        mixed $offset,
    ): bool {
        return isset($this->resolve()[$offset]);
    }

    public function offsetGet(
        mixed $offset,
    ): mixed {
        return $this->resolve()[$offset] ?? null;
    }

    public function offsetSet(
        mixed $offset,
        mixed $value,
    ): void {
        $resolved = $this->resolve();

        if (null === $offset) {
            $resolved[] = $value;
        } else {
            $resolved[$offset] = $value;
        }
    }

    public function offsetUnset(
        mixed $offset,
    ): void {
        $resolved = $this->resolve();
        unset($resolved[$offset]);
    }

    /**
     * @return array<TKey, TValue>
     */
    private function resolve(): array
    {
        return $this->memo ??= ($this->producer)();
    }
}
