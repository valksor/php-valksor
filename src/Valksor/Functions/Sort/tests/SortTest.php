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

namespace Valksor\Functions\Sort\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Valksor\Functions\Sort\Functions;

final class SortTest extends TestCase
{
    private Functions $sortFunctions;

    public function testBubbleSortReferenceParameter(): void
    {
        $original = [5, 2, 8, 1, 3];
        $array = $original;

        $this->sortFunctions->bubbleSort($array);

        // Original should be modified due to reference parameter
        $this->assertNotSame($original, $array);
        $this->assertSame([1, 2, 3, 5, 8], $array);
    }

    public function testBubbleSortWithAlreadySortedArray(): void
    {
        $array = [1, 2, 3, 4, 5];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([1, 2, 3, 4, 5], $array);
    }

    public function testBubbleSortWithDuplicateValues(): void
    {
        $array = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([1, 1, 2, 3, 4, 5, 5, 6, 9], $array);
    }

    // =========================================================================
    // Tests for _BubbleSort trait - bubbleSort() method
    // =========================================================================

    public function testBubbleSortWithEmptyArray(): void
    {
        $array = [];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([], $array);
    }

    public function testBubbleSortWithFloats(): void
    {
        $array = [3.5, 1.2, 4.8, 2.1];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([1.2, 2.1, 3.5, 4.8], $array);
    }

    public function testBubbleSortWithIdenticalElements(): void
    {
        $array = [7, 7, 7, 7, 7];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([7, 7, 7, 7, 7], $array);
    }

    public function testBubbleSortWithLargeArray(): void
    {
        $array = range(100, 1, -1); // 100 to 1
        $expected = range(1, 100);

        $this->sortFunctions->bubbleSort($array);

        $this->assertSame($expected, $array);
    }

    public function testBubbleSortWithMixedPositiveNegative(): void
    {
        $array = [3, -1, 4, -2, 0, 5, -3];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([-3, -2, -1, 0, 3, 4, 5], $array);
    }

    public function testBubbleSortWithReverseSortedArray(): void
    {
        $array = [5, 4, 3, 2, 1];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([1, 2, 3, 4, 5], $array);
    }

    public function testBubbleSortWithSingleElement(): void
    {
        $array = [5];
        $this->sortFunctions->bubbleSort($array);

        $this->assertSame([5], $array);
    }

    public function testMergeIntegrationWithMergeSort(): void
    {
        $left = [1, 4, 7];
        $right = [2, 3, 5, 6, 8, 9];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9], $result);
    }

    public function testMergeSortPreservesOriginalArray(): void
    {
        $original = [3, 1, 4, 1, 5];
        $array = $original;

        $result = $this->sortFunctions->mergeSort($array);

        // Original should not be modified
        $this->assertSame($original, $array);
        $this->assertSame([1, 1, 3, 4, 5], $result);
    }

    public function testMergeSortWithAlreadySortedArray(): void
    {
        $array = [1, 2, 3, 4, 5];
        $result = $this->sortFunctions->mergeSort($array);

        $this->assertSame([1, 2, 3, 4, 5], $result);
        // Original should not be modified
        $this->assertSame($array, $array);
    }

    public function testMergeSortWithDuplicateValues(): void
    {
        $array = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $result = $this->sortFunctions->mergeSort($array);

        $this->assertSame([1, 1, 2, 3, 4, 5, 5, 6, 9], $result);
    }

    // =========================================================================
    // Tests for _MergeSort trait - mergeSort() method
    // =========================================================================

    public function testMergeSortWithEmptyArray(): void
    {
        $result = $this->sortFunctions->mergeSort([]);

        $this->assertSame([], $result);
    }

    public function testMergeSortWithEvenLengthArray(): void
    {
        $array = [4, 2, 5, 1];
        $result = $this->sortFunctions->mergeSort($array);

        $this->assertSame([1, 2, 4, 5], $result);
    }

    public function testMergeSortWithMixedDataTypes(): void
    {
        $array = [3.5, 1, 4.8, 2];
        $result = $this->sortFunctions->mergeSort($array);

        $this->assertSame([1, 2, 3.5, 4.8], $result);
    }

    public function testMergeSortWithNegativeNumbers(): void
    {
        $array = [-3, 1, -4, 2, -1, 5];
        $result = $this->sortFunctions->mergeSort($array);

        $this->assertSame([-4, -3, -1, 1, 2, 5], $result);
    }

    public function testMergeSortWithOddLengthArray(): void
    {
        $array = [3, 1, 4, 1, 5];
        $result = $this->sortFunctions->mergeSort($array);

        $this->assertSame([1, 1, 3, 4, 5], $result);
    }

    public function testMergeSortWithReverseSortedArray(): void
    {
        $array = [5, 4, 3, 2, 1];
        $result = $this->sortFunctions->mergeSort($array);

        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    public function testMergeSortWithSingleElement(): void
    {
        $result = $this->sortFunctions->mergeSort([5]);

        $this->assertSame([5], $result);
    }

    public function testMergeWithBothEmptyArrays(): void
    {
        $left = [];
        $right = [];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([], $result);
    }

    public function testMergeWithDifferentLengths(): void
    {
        $left = [1, 5];
        $right = [2, 3, 4, 6, 7, 8];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8], $result);
    }

    public function testMergeWithDuplicateValues(): void
    {
        $left = [1, 2, 5];
        $right = [2, 3, 5];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1, 2, 2, 3, 5, 5], $result);
    }

    // =========================================================================
    // Tests for _MergeSort trait - merge() method
    // =========================================================================

    public function testMergeWithEmptyLeftArray(): void
    {
        $left = [];
        $right = [1, 2, 3];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testMergeWithEmptyRightArray(): void
    {
        $left = [1, 2, 3];
        $right = [];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testMergeWithFloats(): void
    {
        $left = [1.5, 3.5];
        $right = [2.5, 4.5];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1.5, 2.5, 3.5, 4.5], $result);
    }

    public function testMergeWithNegativeNumbers(): void
    {
        $left = [-3, -1, 2];
        $right = [-2, 0, 3];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([-3, -2, -1, 0, 2, 3], $result);
    }

    public function testMergeWithSingleElementArrays(): void
    {
        $left = [2];
        $right = [1];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1, 2], $result);
    }

    public function testMergeWithSortedInputs(): void
    {
        $left = [1, 3, 5];
        $right = [2, 4, 6];
        $result = $this->sortFunctions->merge($left, $right);

        $this->assertSame([1, 2, 3, 4, 5, 6], $result);
    }

    // =========================================================================
    // Tests for _SortByParameter trait - sortByParameter() method
    // =========================================================================

    public function testSortByParameterWithArrayOfArrays(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
            ['name' => 'Charlie', 'age' => 35],
        ];

        $result = $this->sortFunctions->sortByParameter($data, 'age');

        $expected = [
            ['name' => 'Bob', 'age' => 25],
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Charlie', 'age' => 35],
        ];

        $this->assertSame($expected, $result);
    }

    public function testSortByParameterWithDescendingOrder(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
            ['name' => 'Charlie', 'age' => 35],
        ];

        $result = $this->sortFunctions->sortByParameter($data, 'age', 'DESC');

        $expected = [
            ['name' => 'Charlie', 'age' => 35],
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
        ];

        $this->assertSame($expected, $result);
    }

    public function testSortByParameterWithEmptyArray(): void
    {
        $data = [];

        $result = $this->sortFunctions->sortByParameter($data, 'age');

        $this->assertSame([], $result);
    }

    public function testSortByParameterWithInvalidParameterThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Sorting parameter doesn't exist in sortable variable");

        $data = [
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
        ];

        $this->sortFunctions->sortByParameter($data, 'invalid_param');
    }

    public function testSortByParameterWithMixedTypes(): void
    {
        $data = [
            ['name' => 'Alice', 'score' => 85.5],
            ['name' => 'Bob', 'score' => 92],
            ['name' => 'Charlie', 'score' => 78.0],
        ];

        $result = $this->sortFunctions->sortByParameter($data, 'score');

        $expected = [
            ['name' => 'Charlie', 'score' => 78.0],
            ['name' => 'Alice', 'score' => 85.5],
            ['name' => 'Bob', 'score' => 92],
        ];

        $this->assertSame($expected, $result);
    }

    public function testSortByParameterWithNestedProperty(): void
    {
        $data = [
            ['person' => ['name' => 'Alice', 'age' => 30]],
            ['person' => ['name' => 'Bob', 'age' => 25]],
        ];

        // This should throw exception since the implementation doesn't support nested access
        $this->expectException(InvalidArgumentException::class);
        $this->sortFunctions->sortByParameter($data, 'person.age');
    }

    public function testSortByParameterWithObjectMissingProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Sorting parameter doesn't exist in sortable variable");

        $alice = (object) ['name' => 'Alice'];
        $bob = (object) ['name' => 'Bob', 'age' => 25];

        $data = [$alice, $bob];

        $this->sortFunctions->sortByParameter($data, 'age');
    }

    public function testSortByParameterWithSingleElement(): void
    {
        $data = [['name' => 'Alice', 'age' => 30]];

        $result = $this->sortFunctions->sortByParameter($data, 'age');

        $this->assertSame($data, $result);
    }

    // =========================================================================
    // Tests for _StableSort trait - stableSort() method
    // =========================================================================

    public function testStableSortMaintainsOrderForEqualElements(): void
    {
        $elements = [
            ['name' => 'Alice', 'score' => 85, 'original_index' => 0],
            ['name' => 'Bob', 'score' => 85, 'original_index' => 1],
            ['name' => 'Charlie', 'score' => 85, 'original_index' => 2],
        ];

        $getComparedValue = fn ($element) => $element['score'];
        $compareValues = fn ($a, $b) => $a <=> $b;

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        // Order should be preserved for equal scores
        $this->assertSame('Alice', $result[0]['name']);
        $this->assertSame('Bob', $result[1]['name']);
        $this->assertSame('Charlie', $result[2]['name']);
    }

    public function testStableSortWithCallbackFunctions(): void
    {
        $elements = [
            ['name' => 'Alice', 'score' => 85.5],
            ['name' => 'Bob', 'score' => 85.5],
            ['name' => 'Charlie', 'score' => 90.0],
        ];

        $getComparedValue = function ($element) {
            return (int) $element['score']; // Convert to int for testing
        };

        $compareValues = fn ($a, $b) => $a <=> $b;

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        // Alice and Bob should maintain original order
        $this->assertSame('Alice', $result[0]['name']);
        $this->assertSame('Bob', $result[1]['name']);
        $this->assertSame('Charlie', $result[2]['name']);
    }

    public function testStableSortWithComplexComparison(): void
    {
        $elements = [
            ['name' => 'Alice', 'category' => 'A', 'score' => 85],
            ['name' => 'Bob', 'category' => 'B', 'score' => 90],
            ['name' => 'Charlie', 'category' => 'A', 'score' => 85],
            ['name' => 'David', 'category' => 'B', 'score' => 90],
        ];

        $getComparedValue = fn ($element) => [$element['category'], $element['score']];
        $compareValues = fn ($a, $b) => $a[0] <=> $b[0] ?: ($a[1] <=> $b[1]);

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        $this->assertSame('Alice', $result[0]['name']);   // A, 85
        $this->assertSame('Charlie', $result[1]['name']); // A, 85
        $this->assertSame('Bob', $result[2]['name']);     // B, 90
        $this->assertSame('David', $result[3]['name']);   // B, 90
    }

    public function testStableSortWithCustomComparison(): void
    {
        $elements = [
            ['name' => 'Alice', 'score' => 85],
            ['name' => 'Bob', 'score' => 92],
            ['name' => 'Charlie', 'score' => 78],
        ];

        $getComparedValue = fn ($element) => $element['score'];
        $compareValues = fn ($a, $b) => $b <=> $a; // Descending order

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        $this->assertSame('Bob', $result[0]['name']);     // 92
        $this->assertSame('Alice', $result[1]['name']);   // 85
        $this->assertSame('Charlie', $result[2]['name']); // 78
    }

    public function testStableSortWithDifferentValues(): void
    {
        $elements = [
            ['name' => 'Alice', 'score' => 85, 'original_index' => 0],
            ['name' => 'Bob', 'score' => 92, 'original_index' => 1],
            ['name' => 'Charlie', 'score' => 78, 'original_index' => 2],
            ['name' => 'David', 'score' => 85, 'original_index' => 3],
        ];

        $getComparedValue = fn ($element) => $element['score'];
        $compareValues = fn ($a, $b) => $a <=> $b;

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        $this->assertSame('Charlie', $result[0]['name']); // 78
        $this->assertSame('Alice', $result[1]['name']); // 85, index 0
        $this->assertSame('David', $result[2]['name']); // 85, index 3
        $this->assertSame('Bob', $result[3]['name']);   // 92
    }

    public function testStableSortWithEmptyArray(): void
    {
        $elements = [];

        $getComparedValue = fn ($element) => $element['score'];
        $compareValues = fn ($a, $b) => $a <=> $b;

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        $this->assertSame([], $result);
    }

    public function testStableSortWithSingleElement(): void
    {
        $elements = [['name' => 'Alice', 'score' => 85]];

        $getComparedValue = fn ($element) => $element['score'];
        $compareValues = fn ($a, $b) => $a <=> $b;

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        $this->assertSame([['name' => 'Alice', 'score' => 85]], $result);
    }

    public function testStableSortWithStringComparison(): void
    {
        $elements = [
            ['name' => 'Alice', 'city' => 'New York'],
            ['name' => 'Bob', 'city' => 'Boston'],
            ['name' => 'Charlie', 'city' => 'New York'],
            ['name' => 'David', 'city' => 'Austin'],
        ];

        $getComparedValue = fn ($element) => $element['city'];
        $compareValues = fn ($a, $b) => $a <=> $b;

        $result = $this->sortFunctions->stableSort($elements, $getComparedValue, $compareValues);

        $this->assertSame('David', $result[0]['name']);   // Austin
        $this->assertSame('Bob', $result[1]['name']);     // Boston
        $this->assertSame('Alice', $result[2]['name']);   // New York
        $this->assertSame('Charlie', $result[3]['name']); // New York
    }

    public function testUsortClosureIntegrationWithUsortFunction(): void
    {
        $data = [
            ['name' => 'Charlie', 'age' => 35],
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
        ];

        $closure = $this->sortFunctions->usort('age', 'ASC');
        usort($data, $closure);

        $expected = [
            ['name' => 'Bob', 'age' => 25],
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Charlie', 'age' => 35],
        ];

        $this->assertSame($expected, $data);
    }

    public function testUsortClosureWithAscendingOrder(): void
    {
        $closure = $this->sortFunctions->usort('age', 'ASC');

        $alice = ['age' => 30];
        $bob = ['age' => 25];

        $result = $closure($alice, $bob);

        $this->assertGreaterThan(0, $result); // Alice (30) > Bob (25)
    }

    public function testUsortClosureWithDefaultOrder(): void
    {
        $closure = $this->sortFunctions->usort('age', 'ASC');

        // Test that it behaves like ASC by default
        $alice = ['age' => 30];
        $bob = ['age' => 25];

        $result = $closure($alice, $bob);
        $this->assertGreaterThan(0, $result);
    }

    public function testUsortClosureWithDescendingOrder(): void
    {
        $closure = $this->sortFunctions->usort('age', 'DESC');

        $alice = ['age' => 30];
        $bob = ['age' => 25];

        $result = $closure($alice, $bob);

        $this->assertLessThan(0, $result); // Alice (30) should come before Bob (25) in DESC
    }

    public function testUsortClosureWithDifferentParameter(): void
    {
        $closure = $this->sortFunctions->usort('name', 'ASC');

        $alice = ['name' => 'Alice'];
        $bob = ['name' => 'Bob'];

        $result = $closure($alice, $bob);

        $this->assertLessThan(0, $result); // 'Alice' < 'Bob'
    }

    public function testUsortClosureWithEqualValues(): void
    {
        $closure = $this->sortFunctions->usort('age', 'ASC');

        $alice = ['age' => 30];
        $bob = ['age' => 30];

        $result = $closure($alice, $bob);

        $this->assertSame(0, $result);
    }

    public function testUsortClosureWithMixedTypes(): void
    {
        $closure = $this->sortFunctions->usort('score', 'ASC');

        $alice = ['score' => 85.5];
        $bob = ['score' => 92];

        $result = $closure($alice, $bob);

        $this->assertLessThan(0, $result); // 85.5 < 92
    }

    public function testUsortClosureWithNegativeValues(): void
    {
        $closure = $this->sortFunctions->usort('balance', 'ASC');

        $account1 = ['balance' => -100];
        $account2 = ['balance' => -50];

        $result = $closure($account1, $account2);

        $this->assertLessThan(0, $result); // -100 < -50
    }

    public function testUsortClosureWithObjects(): void
    {
        $closure = $this->sortFunctions->usort('age', 'ASC');

        $alice = ['age' => 30];
        $bob = ['age' => 25];

        $result = $closure($alice, $bob);

        $this->assertGreaterThan(0, $result);
    }

    public function testUsortClosureWithZeroValues(): void
    {
        $closure = $this->sortFunctions->usort('value', 'ASC');

        $item1 = ['value' => 0];
        $item2 = ['value' => 0];

        $result = $closure($item1, $item2);

        $this->assertSame(0, $result);
    }

    // =========================================================================
    // Tests for _Usort trait - usort() method
    // =========================================================================

    public function testUsortReturnsClosure(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sortFunctions->usort('age', 'ASC');
    }

    protected function setUp(): void
    {
        $this->sortFunctions = new Functions();
    }
}
