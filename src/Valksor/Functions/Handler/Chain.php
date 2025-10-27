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

/**
 * @template T
 */
class Chain
{
    /**
     * @param T $value
     */
    public function __construct(
        /**
         * @var T
         */
        private mixed $value,
    ) {
    }

    /**
     * @return T
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * @template U
     *
     * @param callable(T): U $callback
     *
     * @return Chain<U>
     */
    public function pipe(
        callable $callback,
    ): self {
        return new self($callback($this->value));
    }

    /**
     * @param T $value
     *
     * @return Chain<T>
     */
    public static function of(
        mixed $value,
    ): self {
        return new self($value);
    }
}
