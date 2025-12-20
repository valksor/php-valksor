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

// PACKAGE: Covers iteration, array access, property access, and method delegation.

namespace Valksor\Functions\Iteration\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Valksor\Functions\Iteration\Lazy;

final class LazyTest extends TestCase
{
    public function testCallPassesArgumentsToCallable(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'add' => static fn (int $a, int $b): int => $a + $b,
        ]);

        self::assertSame(7, $lazy->add(3, 4));
    }

    public function testCallPassesArgumentsToGetter(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'getValue' => static fn (string $key): string => "value_{$key}",
        ]);

        self::assertSame('value_test', $lazy->value('test'));
    }

    public function testCallPrioritizesDirectMethodOverGetter(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'name' => static fn (): string => 'direct',
            'getName' => static fn (): string => 'getter',
        ]);

        self::assertSame('direct', $lazy->name());
    }

    public function testCallPrioritizesGetterOverIsser(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'getActive' => static fn (): string => 'getter',
            'isActive' => static fn (): string => 'isser',
        ]);

        self::assertSame('getter', $lazy->active());
    }

    public function testCallPrioritizesIsserOverHasser(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'isPermission' => static fn (): string => 'isser',
            'hasPermission' => static fn (): string => 'hasser',
        ]);

        self::assertSame('isser', $lazy->permission());
    }

    public function testCallReturnsNullForNonCallableGetter(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'getName' => 'not a callable',
        ]);

        self::assertNull($lazy->name());
    }

    public function testCallReturnsNullForNonCallableValue(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'notCallable' => 'just a string',
        ]);

        self::assertNull($lazy->notCallable());
    }

    public function testCallReturnsNullForNonExistentMethod(): void
    {
        $lazy = new Lazy(static fn (): array => ['data' => 'value']);

        self::assertNull($lazy->nonExistent());
    }

    public function testCallWithDirectCallableInArray(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'greet' => static fn (string $name): string => "Hello, {$name}!",
        ]);

        self::assertSame('Hello, World!', $lazy->greet('World'));
    }

    public function testCallWithEmptyMethodName(): void
    {
        $lazy = new Lazy(static fn (): array => [
            '' => static fn (): string => 'empty key callable',
        ]);

        $result = $lazy->__call('', []);

        self::assertSame('empty key callable', $result);
    }

    public function testCallWithGetterPrefix(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'getName' => static fn (): string => 'TestName',
        ]);

        self::assertSame('TestName', $lazy->name());
    }

    public function testCallWithHasserPrefix(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'hasPermission' => static fn (): bool => false,
        ]);

        self::assertFalse($lazy->permission());
    }

    public function testCallWithIsserPrefix(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'isActive' => static fn (): bool => true,
        ]);

        self::assertTrue($lazy->active());
    }

    public function testCallWithNoArguments(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'getConstant' => static fn (): int => 42,
        ]);

        self::assertSame(42, $lazy->constant());
    }

    public function testCallWithVariadicArguments(): void
    {
        $lazy = new Lazy(static fn (): array => [
            'sum' => static fn (int ...$numbers): int => array_sum($numbers),
        ]);

        self::assertSame(15, $lazy->sum(1, 2, 3, 4, 5));
    }

    public function testConstructorAcceptsCallableProducer(): void
    {
        $lazy = new Lazy(static fn (): array => ['key' => 'value']);

        self::assertInstanceOf(Lazy::class, $lazy);
    }

    public function testCountReturnsArrayLength(): void
    {
        $lazy = new Lazy(static fn (): array => ['a', 'b', 'c']);

        self::assertSame(3, $lazy->count());
    }

    public function testCountReturnsZeroForEmptyArray(): void
    {
        $lazy = new Lazy(static fn (): array => []);

        self::assertSame(0, $lazy->count());
    }

    public function testCountWorksWithCountFunction(): void
    {
        $lazy = new Lazy(static fn (): array => [1, 2, 3, 4, 5]);

        self::assertCount(5, $lazy);
    }

    public function testGetIteratorPreservesKeys(): void
    {
        $lazy = new Lazy(static fn (): array => [10 => 'a', 20 => 'b', 30 => 'c']);

        $keys = [];

        foreach ($lazy as $key => $value) {
            $keys[] = $key;
        }

        self::assertSame([10, 20, 30], $keys);
    }

    public function testGetIteratorWorksWithEmptyArray(): void
    {
        $lazy = new Lazy(static fn (): array => []);

        $result = [];

        foreach ($lazy as $key => $value) {
            $result[$key] = $value;
        }

        self::assertSame([], $result);
    }

    public function testGetIteratorYieldsAllElements(): void
    {
        $expected = ['first' => 1, 'second' => 2, 'third' => 3];
        $lazy = new Lazy(static fn (): array => $expected);

        $result = [];

        foreach ($lazy as $key => $value) {
            $result[$key] = $value;
        }

        self::assertSame($expected, $result);
    }

    public function testIterationWithMixedValueTypes(): void
    {
        $object = new stdClass();
        $object->prop = 'value';

        $lazy = new Lazy(static fn (): array => [
            'string' => 'text',
            'int' => 42,
            'float' => 3.14,
            'bool' => true,
            'null' => null,
            'array' => [1, 2, 3],
            'object' => $object,
        ]);

        self::assertSame('text', $lazy['string']);
        self::assertSame(42, $lazy['int']);
        self::assertSame(3.14, $lazy['float']);
        self::assertTrue($lazy['bool']);
        self::assertNull($lazy['null']);
        self::assertSame([1, 2, 3], $lazy['array']);
        self::assertSame($object, $lazy['object']);
    }

    public function testLazyWithClosureReturningDynamicData(): void
    {
        $counter = 0;
        $lazy = new Lazy(static function () use (&$counter): array {
            $counter++;

            return ['counter' => $counter];
        });

        self::assertSame(1, $lazy['counter']);
        self::assertSame(1, $lazy['counter']);
    }

    public function testMemoizationOnlyCallsProducerOnce(): void
    {
        $callCount = 0;
        $lazy = new Lazy(static function () use (&$callCount): array {
            $callCount++;

            return ['data' => 'test'];
        });

        $lazy->count();
        $lazy->count();
        $lazy->count();

        self::assertSame(1, $callCount);
    }

    public function testMultipleIterationsUseMemoizedData(): void
    {
        $callCount = 0;
        $lazy = new Lazy(static function () use (&$callCount): array {
            $callCount++;

            return ['a' => 1, 'b' => 2];
        });

        $firstIteration = [];

        foreach ($lazy as $key => $value) {
            $firstIteration[$key] = $value;
        }

        $secondIteration = [];

        foreach ($lazy as $key => $value) {
            $secondIteration[$key] = $value;
        }

        self::assertSame($firstIteration, $secondIteration);
        self::assertSame(1, $callCount);
    }

    public function testOffsetExistsReturnsFalseForMissingKey(): void
    {
        $lazy = new Lazy(static fn (): array => ['exists' => 'value']);

        self::assertFalse(isset($lazy['missing']));
    }

    public function testOffsetExistsReturnsFalseForNullValue(): void
    {
        $lazy = new Lazy(static fn (): array => ['nullKey' => null]);

        self::assertFalse(isset($lazy['nullKey']));
    }

    public function testOffsetExistsReturnsTrueForExistingKey(): void
    {
        $lazy = new Lazy(static fn (): array => ['exists' => 'value']);

        self::assertTrue(isset($lazy['exists']));
    }

    public function testOffsetExistsWithIntegerKey(): void
    {
        $lazy = new Lazy(static fn (): array => [0 => 'zero', 1 => 'one']);

        self::assertTrue(isset($lazy[0]));
        self::assertTrue(isset($lazy[1]));
        self::assertFalse(isset($lazy[2]));
    }

    public function testOffsetGetReturnsNullForMissingKey(): void
    {
        $lazy = new Lazy(static fn (): array => ['key' => 'value']);

        self::assertNull($lazy['missing']);
    }

    public function testOffsetGetReturnsValueForExistingKey(): void
    {
        $lazy = new Lazy(static fn (): array => ['key' => 'expected']);

        self::assertSame('expected', $lazy['key']);
    }

    public function testOffsetGetWorksWithNumericKeys(): void
    {
        $lazy = new Lazy(static fn (): array => [0 => 'first', 1 => 'second']);

        self::assertSame('first', $lazy[0]);
        self::assertSame('second', $lazy[1]);
    }

    public function testOffsetSetWithKeyDoesNotModifyMemoizedArray(): void
    {
        $lazy = new Lazy(static fn (): array => ['original' => 'value']);

        $lazy['new'] = 'added';

        self::assertNull($lazy['new']);
        self::assertSame('value', $lazy['original']);
    }

    public function testOffsetSetWithNullKeyDoesNotModifyMemoizedArray(): void
    {
        $lazy = new Lazy(static fn (): array => ['existing']);

        $lazy[] = 'appended';

        self::assertSame(1, $lazy->count());
    }

    public function testOffsetUnsetDoesNotModifyMemoizedArray(): void
    {
        $lazy = new Lazy(static fn (): array => ['key' => 'value']);

        unset($lazy['key']);

        self::assertSame('value', $lazy['key']);
    }
}
