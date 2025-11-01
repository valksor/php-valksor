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

namespace Valksor\Functions\Pagination\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Valksor\Functions\Pagination\Pagination;

use function count;

final class PaginationTest extends TestCase
{
    private Pagination $pagination;

    // Mathematical boundary conditions
    public function testBoundaryWhereCurrentEqualsBreakpoint(): void
    {
        $result = $this->pagination->paginate(9, 20, 5);

        $this->assertSame([1, 2, 3, 4, 5, 6, -1, 19, 20], $result);
    }

    public function testBoundaryWhereCurrentEqualsBreakpointFromEnd(): void
    {
        $result = $this->pagination->paginate(9, 20, 16);

        $this->assertSame([1, 2, -1, 15, 16, 17, 18, 19, 20], $result);
    }

    public function testBoundaryWithCurrentAtQuarterPoint(): void
    {
        $result = $this->pagination->paginate(9, 100, 25);

        $this->assertSame([1.0, 2.0, -1, 24.0, 25.0, 26.0, -1, 99.0, 100.0], $result);
    }

    public function testBoundaryWithCurrentAtThreeQuarterPoint(): void
    {
        $result = $this->pagination->paginate(9, 100, 75);

        $this->assertSame([1.0, 2.0, -1, 74.0, 75.0, 76.0, -1, 99.0, 100.0], $result);
    }

    public function testBoundaryWithTotalExactlyTwiceVisible(): void
    {
        $result = $this->pagination->paginate(5, 10, 5);

        $this->assertSame([1.0, -1, 5.0, -1, 10.0], $result);
    }

    public function testBoundaryWithTotalJustAboveVisible(): void
    {
        $result = $this->pagination->paginate(5, 6, 3);

        $this->assertSame([1, 2, 3, -1, 6], $result);
    }

    // Double omitted page scenarios (middle positioning)
    public function testDoubleOmittedWithCurrentInMiddle(): void
    {
        $result = $this->pagination->paginate(7, 20, 10);

        $this->assertSame([1.0, -1, 9.0, 10.0, 11.0, -1, 20.0], $result);
    }

    public function testDoubleOmittedWithCurrentSlightlyLeftOfMiddle(): void
    {
        $result = $this->pagination->paginate(7, 20, 9);

        $this->assertSame([1.0, -1, 8.0, 9.0, 10.0, -1, 20.0], $result);
    }

    public function testDoubleOmittedWithCurrentSlightlyRightOfMiddle(): void
    {
        $result = $this->pagination->paginate(7, 20, 11);

        $this->assertSame([1.0, -1, 10.0, 11.0, 12.0, -1, 20.0], $result);
    }

    public function testEvenVisibleWithOddTotal(): void
    {
        $result = $this->pagination->paginate(8, 21, 11);

        $this->assertSame([1.0, 2.0, -1, 10.0, 11.0, 12.0, -1, 21.0], $result);
    }

    public function testLargePageNumbersWithCurrentInMiddle(): void
    {
        $result = $this->pagination->paginate(9, 100, 50);

        $this->assertSame([1.0, 2.0, -1, 49.0, 50.0, 51.0, -1, 99.0, 100.0], $result);
    }

    public function testLargePageNumbersWithCurrentNearEnd(): void
    {
        $result = $this->pagination->paginate(9, 100, 95);

        $this->assertSame([1.0, 2.0, -1, 94.0, 95.0, 96.0, -1, 99.0, 100.0], $result);
    }

    // Large page number scenarios
    public function testLargePageNumbersWithCurrentNearStart(): void
    {
        $result = $this->pagination->paginate(9, 100, 5);

        $this->assertSame([1, 2, 3, 4, 5, 6, -1, 99, 100], $result);
    }

    // Edge cases with odd/even calculations
    public function testOddVisibleWithEvenTotal(): void
    {
        $result = $this->pagination->paginate(7, 20, 10);

        $this->assertSame([1.0, -1, 9.0, 10.0, 11.0, -1, 20.0], $result);
    }

    public function testPaginateThrowsExceptionForCurrentAboveTotal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Current page (15) should not be higher than total number of pages (10)');

        $this->pagination->paginate(5, 10, 15);
    }

    public function testPaginateThrowsExceptionForInvalidIndicatorWithinRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Omitted pages indicator (5) should not be between 1 and total number of pages (10)');

        $this->pagination->paginate(5, 10, 3, 5);
    }

    public function testPaginateThrowsExceptionForNegativeCurrent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Current page (-3) should not be lower than 1');

        $this->pagination->paginate(5, 10, -3);
    }

    public function testPaginateThrowsExceptionForNegativeTotal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Total number of pages (-5) should not be lower than 1');

        $this->pagination->paginate(5, -5, 1);
    }

    public function testPaginateThrowsExceptionForVisibleBelowMinimum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Maximum of number of visible pages (4) should be at least 5');

        $this->pagination->paginate(4, 10, 5);
    }

    public function testPaginateThrowsExceptionForZeroCurrent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Current page (0) should not be lower than 1');

        $this->pagination->paginate(5, 10, 0);
    }

    public function testPaginateThrowsExceptionForZeroTotal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Total number of pages (0) should not be lower than 1');

        $this->pagination->paginate(5, 0, 1);
    }

    public function testPaginateWithCurrentPageFirst(): void
    {
        $result = $this->pagination->paginate(5, 10, 1);

        $this->assertSame([1, 2, -1, 9, 10], $result);
    }

    public function testPaginateWithCurrentPageInMiddle(): void
    {
        $result = $this->pagination->paginate(7, 20, 10);

        $this->assertSame([1.0, -1, 9.0, 10.0, 11.0, -1, 20.0], $result);
    }

    public function testPaginateWithCurrentPageLast(): void
    {
        $result = $this->pagination->paginate(5, 10, 10);

        $this->assertSame([1, 2, -1, 9, 10], $result);
    }

    public function testPaginateWithCurrentPageNearEnd(): void
    {
        $result = $this->pagination->paginate(5, 10, 9);

        $this->assertSame([1, -1, 8, 9, 10], $result);
    }

    public function testPaginateWithCurrentPageNearStart(): void
    {
        $result = $this->pagination->paginate(5, 10, 2);

        $this->assertSame([1, 2, 3, -1, 10], $result);
    }

    public function testPaginateWithCustomIndicator(): void
    {
        $result = $this->pagination->paginate(5, 10, 2, 0);

        $this->assertSame([1, 2, 3, 0, 10], $result);
    }

    public function testPaginateWithDefaultIndicator(): void
    {
        $result = $this->pagination->paginate(5, 10, 2);

        $this->assertSame([1, 2, 3, -1, 10], $result);
    }

    public function testPaginateWithHighPositiveIndicator(): void
    {
        $result = $this->pagination->paginate(5, 10, 5, 999);

        $this->assertSame([1.0, 999, 5.0, 999, 10.0], $result);
    }

    public function testPaginateWithIndicatorAtFirstPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Omitted pages indicator (1) should not be between 1 and total number of pages (10)');

        $this->pagination->paginate(5, 10, 3, 1);
    }

    public function testPaginateWithIndicatorAtLastPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Omitted pages indicator (10) should not be between 1 and total number of pages (10)');

        $this->pagination->paginate(5, 10, 3, 10);
    }

    // LEVEL 2 TESTS: Edge Cases & Validation

    public function testPaginateWithMinimumVisible(): void
    {
        $result = $this->pagination->paginate(5, 10, 5);

        $this->assertSame([1.0, -1, 5.0, -1, 10.0], $result);
    }

    public function testPaginateWithNegativeIndicator(): void
    {
        $result = $this->pagination->paginate(5, 10, 5, -1);

        $this->assertSame([1.0, -1, 5.0, -1, 10.0], $result);
    }

    public function testPaginateWithSmallRange(): void
    {
        $result = $this->pagination->paginate(5, 3, 2);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testPaginateWithTotalEqualsVisible(): void
    {
        $result = $this->pagination->paginate(5, 5, 3);

        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    // LEVEL 1 TESTS: Basic Functionality

    public function testPaginateWithTotalLessThanVisible(): void
    {
        $result = $this->pagination->paginate(10, 5, 3);

        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    public function testPaginateWithZeroIndicator(): void
    {
        $result = $this->pagination->paginate(5, 10, 5, 0);

        $this->assertSame([1.0, 0, 5.0, 0, 10.0], $result);
    }

    public function testPaginationResultContainsOnlyNumeric(): void
    {
        foreach ($this->pagination->paginate(7, 20, 10) as $value) {
            $this->assertIsNumeric($value);
        }
    }

    public function testPaginationResultIsArray(): void
    {
        $result = $this->pagination->paginate(5, 10, 3);

        $this->assertIsArray($result);
    }

    public function testPaginationStateDoesNotPersistBetweenCalls(): void
    {
        $result1 = $this->pagination->paginate(5, 10, 2);
        $result2 = $this->pagination->paginate(5, 20, 10);

        $this->assertSame([1, 2, 3, -1, 10], $result1);
        $this->assertSame([1.0, -1, 10.0, -1, 20.0], $result2);
    }

    public function testPaginationWithSameParametersReturnsSameResult(): void
    {
        $result1 = $this->pagination->paginate(7, 20, 10);
        $result2 = $this->pagination->paginate(7, 20, 10);

        $this->assertSame($result1, $result2);
    }

    // Single omitted page scenarios near end
    public function testSingleOmittedNearEndWithCurrentPage17(): void
    {
        $result = $this->pagination->paginate(7, 20, 17);

        $this->assertSame([1, -1, 16, 17, 18, 19, 20], $result);
    }

    public function testSingleOmittedNearEndWithCurrentPage18(): void
    {
        $result = $this->pagination->paginate(7, 20, 18);

        $this->assertSame([1, 2, -1, 17, 18, 19, 20], $result);
    }

    public function testSingleOmittedNearEndWithCurrentPage19(): void
    {
        $result = $this->pagination->paginate(7, 20, 19);

        $this->assertSame([1, 2, -1, 17, 18, 19, 20], $result);
    }

    public function testSingleOmittedNearEndWithCurrentPage20(): void
    {
        $result = $this->pagination->paginate(7, 20, 20);

        $this->assertSame([1, 2, 3, -1, 18, 19, 20], $result);
    }

    // LEVEL 3 TESTS: Complex Scenarios

    // Single omitted page scenarios near start
    public function testSingleOmittedNearStartWithCurrentPage1(): void
    {
        $result = $this->pagination->paginate(7, 20, 1);

        $this->assertSame([1, 2, 3, -1, 18, 19, 20], $result);
    }

    public function testSingleOmittedNearStartWithCurrentPage2(): void
    {
        $result = $this->pagination->paginate(7, 20, 2);

        $this->assertSame([1, 2, 3, 4, -1, 19, 20], $result);
    }

    public function testSingleOmittedNearStartWithCurrentPage3(): void
    {
        $result = $this->pagination->paginate(7, 20, 3);

        $this->assertSame([1, 2, 3, 4, -1, 19, 20], $result);
    }

    public function testSingleOmittedNearStartWithCurrentPage4(): void
    {
        $result = $this->pagination->paginate(7, 20, 4);

        $this->assertSame([1, 2, 3, 4, 5, -1, 20], $result);
    }

    // Additional validation scenarios
    public function testWithCurrentExactlyAtVisibleHalf(): void
    {
        $result = $this->pagination->paginate(9, 18, 9);

        $this->assertSame([1.0, 2.0, -1, 8.0, 9.0, 10.0, -1, 17.0, 18.0], $result);
    }

    public function testWithCurrentJustAboveVisibleHalf(): void
    {
        $result = $this->pagination->paginate(9, 18, 10);

        $this->assertSame([1.0, 2.0, -1, 9.0, 10.0, 11.0, -1, 17.0, 18.0], $result);
    }

    public function testWithEvenVisibleCount(): void
    {
        $result = $this->pagination->paginate(8, 30, 15);

        $this->assertSame([1.0, 2.0, -1, 14.0, 15.0, 16.0, -1, 30.0], $result);
    }

    public function testWithHighVisibleAndLargeTotal(): void
    {
        $result = $this->pagination->paginate(15, 1000, 500);

        $this->assertContains(1.0, $result);
        $this->assertContains(500.0, $result);
        $this->assertContains(1000.0, $result);
        $this->assertLessThanOrEqual(15, count($result));
    }

    public function testWithHighVisibleCount(): void
    {
        $result = $this->pagination->paginate(15, 30, 15);

        $this->assertSame([1.0, 2.0, 3.0, -1, 12.0, 13.0, 14.0, 15.0, 16.0, 17.0, 18.0, -1, 28.0, 29.0, 30.0], $result);
    }

    // Extreme values
    public function testWithMinimumVisibleAndLargeTotal(): void
    {
        $result = $this->pagination->paginate(5, 1000, 500);

        $this->assertSame([1.0, -1, 500.0, -1, 1000.0], $result);
    }

    // Different visible counts
    public function testWithOddVisibleCount(): void
    {
        $result = $this->pagination->paginate(9, 30, 15);

        $this->assertSame([1.0, 2.0, -1, 14.0, 15.0, 16.0, -1, 29.0, 30.0], $result);
    }

    protected function setUp(): void
    {
        $this->pagination = new Pagination();
    }
}
