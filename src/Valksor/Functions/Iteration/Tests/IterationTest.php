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

namespace Valksor\Functions\Iteration\Tests;

use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\TestCase;
use Valksor\Functions\Iteration\Exception\FieldNotFoundException;
use Valksor\Functions\Iteration\Functions;
use Valksor\Functions\Iteration\Traits\_Cases;
use Valksor\Functions\Iteration\Traits\_Value;
use ValueError;

enum SampleBacked: string
{
    use _Cases;

    case One = 'one';
    case Two = 'two';
}

final class IterationTest extends TestCase
{
    private Functions $iteration;

    public function testAddElementIfNotExistsAddsAndKeepsUniqueValues(): void
    {
        $data = [];
        $this->iteration->addElementIfNotExists($data, 'alpha', 'first');
        $this->iteration->addElementIfNotExists($data, 'alpha', 'first');
        $this->iteration->addElementIfNotExists($data, 'beta', 'second');
        $this->iteration->addElementIfNotExists($data, 'gamma', 'first');

        self::assertSame(['first' => 'gamma', 'second' => 'beta'], $data);
    }

    public function testArrayFlipRecursiveFlipsAndKeepsNestedValues(): void
    {
        $object = (object) ['id' => 10];
        $input = [
            'first' => 'value',
            'second' => ['inner' => 42],
            'third' => $object,
        ];

        $result = $this->iteration->arrayFlipRecursive($input);

        self::assertSame('first', $result['value']);
        self::assertSame(['inner' => 42], $result['second']);
        self::assertSame($object, $result['third']);
    }

    public function testArrayFlipRecursiveRejectsUnsupportedTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->iteration->arrayFlipRecursive(['invalid' => 1.5]);
    }

    public function testArrayIntersectKeyRecursiveMergesNestedKeys(): void
    {
        $first = [
            'a' => 1,
            'b' => ['x' => 1, 'y' => 2],
            'c' => 3,
        ];
        $second = [
            'a' => 9,
            'b' => ['x' => 8],
            'd' => 0,
        ];

        self::assertSame(
            [
                'a' => 1,
                'b' => ['x' => 1],
            ],
            $this->iteration->arrayIntersectKeyRecursive($first, $second),
        );
    }

    public function testArrayToStringConvertsNestedStructure(): void
    {
        $result = $this->iteration->arrayToString(['a' => 1, 'b' => ['c' => 2]]);

        self::assertSame("['a' => 1, 'b' => ['c' => 2]]", $result);
    }

    public function testArrayToStringHandlesEmptyArray(): void
    {
        self::assertSame('[]', $this->iteration->arrayToString([]));
    }

    public function testArrayValuesFilteredSupportsPrefixAndSuffix(): void
    {
        $input = [
            'pre_one' => 1,
            'pre_two' => 2,
            'one_post' => 3,
        ];

        self::assertSame([1, 2], $this->iteration->arrayValuesFiltered($input, 'pre'));
        self::assertSame([3], $this->iteration->arrayValuesFiltered($input, 'post', start: false));
    }

    public function testCasesTraitReturnsBackingValues(): void
    {
        self::assertSame(['one', 'two'], SampleBacked::getCases());
    }

    public function testCasesTraitReturnsEmptyForNonEnum(): void
    {
        $class = new class {
            use _Cases;

            public static function cases(): array
            {
                return [];
            }
        };

        self::assertSame([], $class::getCases());
    }

    public function testFirstMatchAsStringReturnsExpectedMatch(): void
    {
        $keys = ['missing', 'found'];
        $haystack = ['found' => 123];

        self::assertSame('123', $this->iteration->firstMatchAsString($keys, $haystack));
        self::assertNull($this->iteration->firstMatchAsString(['missing'], $haystack));
    }

    public function testHaveCommonElementsDetectsOverlap(): void
    {
        self::assertTrue($this->iteration->haveCommonElements([1, 2], [2, 3]));
        self::assertFalse($this->iteration->haveCommonElements([1], [3]));
    }

    public function testIsAssociativeHonoursAllowList(): void
    {
        self::assertFalse($this->iteration->isAssociative([]));
        self::assertFalse($this->iteration->isAssociative([1, 2]));
        self::assertTrue($this->iteration->isAssociative([1, 2], allowList: true));
        self::assertTrue($this->iteration->isAssociative(['key' => 'value']));
    }

    public function testIsEmptyRecursivelyChecksNestedArrays(): void
    {
        self::assertTrue($this->iteration->isEmpty([0, '', [null]]));
        self::assertFalse($this->iteration->isEmpty(['', ['value']]));
    }

    public function testIsMultiDimensionalDetectsNestedArrays(): void
    {
        self::assertFalse($this->iteration->isMultiDimensional([1, 2]));
        self::assertTrue($this->iteration->isMultiDimensional([1, [2]]));
    }

    public function testIsSortableHandlesArraysAndObjects(): void
    {
        $object = new class {
            public string $name = 'alpha';
        };

        self::assertTrue($this->iteration->isSortable(['name' => 'alpha'], 'name'));
        self::assertTrue($this->iteration->isSortable($object, 'name'));
        self::assertFalse($this->iteration->isSortable('string', 'name'));
    }

    public function testIsSortedAscendingIntsValidatesSequence(): void
    {
        self::assertTrue($this->iteration->isSortedAscendingInts([]));
        self::assertTrue($this->iteration->isSortedAscendingInts([1, 2, 3]));
        self::assertFalse($this->iteration->isSortedAscendingInts(['1', 2]));
        self::assertFalse($this->iteration->isSortedAscendingInts([1, '0']));
    }

    /**
     * @throws JsonException
     */
    public function testJsonDecodeParsesAndReportsErrors(): void
    {
        $decoded = $this->iteration->jsonDecode('{"value": 1}', true, flags: 0b0001);
        self::assertSame('1', (string) $decoded['value']);

        $this->expectException(JsonException::class);
        $this->iteration->jsonDecode('{invalid json}');
    }

    /**
     * @throws JsonException
     */
    public function testJsonEncodeAppliesDefaultFlagsAndPrettyPrint(): void
    {
        self::assertSame('{"foo":1.0}', $this->iteration->jsonEncode(['foo' => 1.0]));

        $pretty = $this->iteration->jsonEncode(['foo' => 1], JSON_PRETTY_PRINT);
        self::assertStringContainsString("\n", $pretty);
        self::assertStringContainsString('"foo": 1', $pretty);
    }

    public function testMakeMultiDimensionalWrapsScalars(): void
    {
        self::assertSame([[1], [2]], $this->iteration->makeMultiDimensional([1, 2]));

        $alreadyMulti = [['value' => 1]];
        self::assertSame($alreadyMulti, $this->iteration->makeMultiDimensional($alreadyMulti));
    }

    public function testMakeOneDimensionFlattensWithOptions(): void
    {
        $input = ['user' => ['profile' => ['name' => 'Alice', 'age' => 30]]];

        // Test basic flattening - generator yields correct key-value pairs
        $flattened = [];

        foreach ($this->iteration->makeOneDimension($input) as $key => $value) {
            $flattened[$key] = $value;
        }
        self::assertSame('Alice', $flattened['user.profile.name']);
        self::assertSame(30, $flattened['user.profile.age']);

        // Test onlyLast parameter - should skip intermediate levels
        $onlyLast = [];

        foreach ($this->iteration->makeOneDimension($input, onlyLast: true) as $key => $value) {
            $onlyLast[$key] = $value;
        }
        self::assertSame(['user.profile.name' => 'Alice', 'user.profile.age' => 30], $onlyLast);

        // Test maxDepth parameter - should limit recursion depth
        $limited = [];

        foreach ($this->iteration->makeOneDimension($input, maxDepth: 0) as $key => $value) {
            $limited[$key] = $value;
        }
        self::assertSame(['user' => ['profile' => ['name' => 'Alice', 'age' => 30]]], $limited);

        // Test allowList parameter - should treat arrays as both list and associative
        $withList = [];

        foreach ($this->iteration->makeOneDimension(['items' => [1, 2]], allowList: true) as $key => $value) {
            $withList[$key] = $value;
        }
        self::assertSame(
            [
                'items.0' => 1,
                'items.1' => 2,
                'items' => [1, 2],
            ],
            $withList,
        );
    }

    public function testPickThrowsWhenRandomizerSeedInvalid(): void
    {
        $this->expectException(ValueError::class);
        $this->iteration->pick(['first' => 1, 'second' => 2]);
    }

    public function testRecursiveKSortSortsNestedArrays(): void
    {
        $array = ['b' => ['d' => 2, 'c' => 1], 'a' => 3];
        $this->iteration->recursiveKSort($array);

        self::assertSame(['a' => 3, 'b' => ['c' => 1, 'd' => 2]], $array);
    }

    public function testRemoveFromArrayRemovesValue(): void
    {
        $values = [1, 2, 2, 3];
        $this->iteration->removeFromArray($values, 2);

        self::assertSame([0 => 1, 3 => 3], $values);
    }

    public function testSwapArrayExchangesValuesWhenDifferent(): void
    {
        $array = ['first' => 'a', 'second' => 'b'];
        $this->iteration->swapArray($array, 'first', 'second');
        self::assertSame(['first' => 'b', 'second' => 'a'], $array);

        $equalValues = ['first' => 'same', 'second' => 'same'];
        $this->iteration->swapArray($equalValues, 'first', 'second');
        self::assertSame(['first' => 'same', 'second' => 'same'], $equalValues);
    }

    public function testUniqueMapRemovesDuplicateMaps(): void
    {
        $maps = [['a' => 1], ['a' => 1], ['b' => 2]];
        $this->iteration->uniqueMap($maps);

        self::assertCount(2, $maps);
        self::assertSame(['a' => 1], $maps[0]);
    }

    public function testUniqueRespectsKeepKeysAndMultiDimensional(): void
    {
        self::assertSame([1, 2], $this->iteration->unique([1, 1, 2]));
        self::assertSame(['a' => 1], $this->iteration->unique(['a' => 1, 'b' => 1], keepKeys: true));

        $multi = [[1], [1]];
        self::assertSame($multi, $this->iteration->unique($multi));
    }

    public function testUnpackRecreatesNestedStructure(): void
    {
        $flat = ['user.profile.name' => 'Bob', 'user.profile.age' => 28];
        $nested = $this->iteration->unpack($flat);

        self::assertSame('Bob', $nested['user']['profile']['name']);
        self::assertSame(28, $nested['user']['profile']['age']);
    }

    public function testValueTraitRetrievesFromArrayAndObject(): void
    {
        $helper = new class {
            use _Value;
        };

        $object = new class {
            public string $name = 'Test';
        };

        self::assertSame('value', $helper->value(['field' => 'value'], 'field'));
        self::assertSame('Test', $helper->value($object, 'name'));

        $this->expectException(FieldNotFoundException::class);
        $helper->value([], 'missing');
    }

    protected function setUp(): void
    {
        $this->iteration = new Functions();
    }
}
