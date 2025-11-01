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

namespace Valksor\Functions\Handler\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Valksor\Functions\Handler\Chain;

use function strtoupper;

final class ChainTest extends TestCase
{
    public function testChainPreservesTypeInformation(): void
    {
        $stringChain = Chain::of('test');
        $this->assertSame('test', $stringChain->get());

        $intChain = Chain::of(42);
        $this->assertSame(42, $intChain->get());

        $arrayChain = Chain::of([1, 2, 3]);
        $this->assertSame([1, 2, 3], $arrayChain->get());
    }

    public function testComplexPipelineWithDataProcessing(): void
    {
        $data = [
            ['name' => 'Alice', 'score' => 85],
            ['name' => 'Bob', 'score' => 92],
            ['name' => 'Charlie', 'score' => 78],
        ];

        $result = Chain::of($data)
            ->pipe(fn ($items) => array_filter($items, static fn ($item) => $item['score'] >= 80))
            ->pipe(fn ($items) => array_map(static fn ($item) => $item['name'], $items))
            ->pipe(fn ($items) => implode(', ', $items))
            ->get();

        $this->assertSame('Alice, Bob', $result);
    }

    // Complex scenarios

    public function testComplexPipelineWithStringManipulation(): void
    {
        $result = Chain::of('  hello world  ')
            ->pipe(fn ($value) => trim($value))
            ->pipe(fn ($value) => strtoupper($value))
            ->pipe(fn ($value) => str_replace(' ', '_', $value))
            ->get();

        $this->assertSame('HELLO_WORLD', $result);
    }

    public function testConstructorWithArray(): void
    {
        $data = [1, 2, 3];
        $chain = new Chain($data);
        $this->assertSame($data, $chain->get());
    }

    public function testConstructorWithInteger(): void
    {
        $chain = new Chain(42);
        $this->assertSame(42, $chain->get());
    }

    public function testConstructorWithObject(): void
    {
        $obj = new stdClass();
        $obj->name = 'test';
        $chain = new Chain($obj);
        $this->assertSame($obj, $chain->get());
    }
    // Constructor tests

    public function testConstructorWithValue(): void
    {
        $chain = new Chain('test');
        $this->assertSame('test', $chain->get());
    }

    // get() method tests

    public function testGetReturnsConstructorValue(): void
    {
        $chain = new Chain('initial');
        $this->assertSame('initial', $chain->get());
    }

    public function testGetReturnsOfValue(): void
    {
        $chain = Chain::of('factory');
        $this->assertSame('factory', $chain->get());
    }

    public function testLongPipelineChain(): void
    {
        $result = Chain::of(1)
            ->pipe(fn ($n) => $n + 1)  // 2
            ->pipe(fn ($n) => $n * 2)   // 4
            ->pipe(fn ($n) => $n + 3)   // 7
            ->pipe(fn ($n) => $n * 3)   // 21
            ->pipe(fn ($n) => $n - 1)   // 20
            ->pipe(fn ($n) => intdiv($n, 4))   // 5
            ->get();

        $this->assertSame(5, $result);
    }

    public function testMultipleIndependentChains(): void
    {
        $chain1 = Chain::of(5)->pipe(fn ($n) => $n * 2);
        $chain2 = Chain::of(5)->pipe(fn ($n) => $n + 10);

        $this->assertSame(10, $chain1->get());
        $this->assertSame(15, $chain2->get());
    }

    public function testOfCreatesChainWithInteger(): void
    {
        $chain = Chain::of(100);
        $this->assertSame(100, $chain->get());
    }

    public function testOfCreatesChainWithNull(): void
    {
        $chain = Chain::of(null);
        $this->assertNull($chain->get());
    }

    // Static factory method tests

    public function testOfCreatesChainWithValue(): void
    {
        $chain = Chain::of('test');
        $this->assertSame('test', $chain->get());
    }

    public function testPipeChangesType(): void
    {
        $result = Chain::of('123')->pipe(fn ($value) => (int) $value);
        $this->assertSame(123, $result->get());
    }

    public function testPipeDoesNotMutateOriginalChain(): void
    {
        $chain = Chain::of(5);
        $chain->pipe(fn ($value) => $value * 10);

        $this->assertSame(5, $chain->get());
    }

    public function testPipeReturnsNewChainInstance(): void
    {
        $chain1 = Chain::of('test');
        $chain2 = $chain1->pipe(fn ($value) => strtoupper($value));

        $this->assertNotSame($chain1, $chain2);
        $this->assertSame('test', $chain1->get());
        $this->assertSame('TEST', $chain2->get());
    }

    // pipe() method tests

    public function testPipeTransformsValue(): void
    {
        $result = Chain::of('hello')->pipe(fn ($value) => strtoupper($value));
        $this->assertSame('HELLO', $result->get());
    }

    public function testPipeWithArrayOperations(): void
    {
        $result = Chain::of([1, 2, 3])
            ->pipe(fn ($value) => array_map(static fn ($n) => $n * 2, $value))
            ->pipe(fn ($value) => array_sum($value));
        $this->assertSame(12, $result->get());
    }

    public function testPipeWithBoolean(): void
    {
        $result = Chain::of(true)
            ->pipe(fn ($value) => !$value)
            ->pipe(fn ($value) => $value ? 'yes' : 'no')
            ->get();

        $this->assertSame('no', $result);
    }

    public function testPipeWithCallableObject(): void
    {
        $callable = new class {
            public function __invoke(
                string $value,
            ): string {
                return strtoupper($value);
            }
        };

        $result = Chain::of('test')->pipe($callable);
        $this->assertSame('TEST', $result->get());
    }

    public function testPipeWithMultipleOperations(): void
    {
        $result = Chain::of('hello')
            ->pipe(fn ($value) => strtoupper($value))
            ->pipe(fn ($value) => $value . ' WORLD');
        $this->assertSame('HELLO WORLD', $result->get());
    }

    public function testPipeWithNull(): void
    {
        $result = Chain::of(null)->pipe(fn ($value) => $value ?? 'default');
        $this->assertSame('default', $result->get());
    }

    public function testPipeWithNumericOperations(): void
    {
        $result = Chain::of(10)
            ->pipe(fn ($value) => $value * 2)
            ->pipe(fn ($value) => $value + 5)
            ->pipe(fn ($value) => intdiv($value, 5));
        $this->assertSame(5, $result->get());
    }

    public function testPipeWithObjectTransformation(): void
    {
        $obj = new stdClass();
        $obj->value = 10;

        $result = Chain::of($obj)->pipe(fn ($obj) => $obj->value * 2);
        $this->assertSame(20, $result->get());
    }
}
