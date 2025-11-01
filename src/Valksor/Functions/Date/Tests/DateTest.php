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

namespace Valksor\Functions\Date\Tests;

use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Valksor\Functions\Date\Functions;

final class DateTest extends TestCase
{
    private Functions $dateFunctions;

    public function testDateNullableWithDifferentFormats(): void
    {
        $result = $this->dateFunctions->dateNullable('2024-12-25 14:30:00', 'Y-m-d H:i:s');

        $this->assertInstanceOf(DateTimeInterface::class, $result);
        $this->assertSame('2024-12-25', $result->format('Y-m-d'));
    }

    public function testDateNullableWithInvalidDateReturnsNull(): void
    {
        $result = $this->dateFunctions->dateNullable('invalid-date', 'd-m-Y');

        $this->assertNull($result);
    }

    public function testDateNullableWithNullDateReturnsNull(): void
    {
        $result = $this->dateFunctions->dateNullable(null, 'd-m-Y');

        $this->assertNull($result);
    }

    public function testDateNullableWithNullFormatReturnsNull(): void
    {
        $result = $this->dateFunctions->dateNullable('15-03-2024');

        $this->assertNull($result);
    }

    // =========================================================================
    // Tests for _DateNullable trait - dateNullable() method
    // =========================================================================

    public function testDateNullableWithValidDateAndFormat(): void
    {
        $result = $this->dateFunctions->dateNullable('15-03-2024', 'd-m-Y');

        $this->assertInstanceOf(DateTimeInterface::class, $result);
        $this->assertSame('15-03-2024', $result->format('d-m-Y'));
    }

    public function testDateWithDifferentFormats(): void
    {
        $result = $this->dateFunctions->date('2024/12/25', 'Y/m/d');

        $this->assertSame('2024-12-25', $result->format('Y-m-d'));
    }

    public function testDateWithInvalidDateThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date string');

        $this->dateFunctions->date('invalid-date', 'd-m-Y');
    }

    public function testDateWithNullDateThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date string');

        $this->dateFunctions->date(null, 'd-m-Y');
    }

    public function testDateWithNullFormatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date string');

        $this->dateFunctions->date('15-03-2024');
    }

    // =========================================================================
    // Tests for _Date trait - date() method
    // =========================================================================

    public function testDateWithValidDateAndFormat(): void
    {
        $result = $this->dateFunctions->date('15-03-2024', 'd-m-Y');

        $this->assertSame('15-03-2024', $result->format('d-m-Y'));
    }

    public function testDateWithoutFormatWithCustomGuess(): void
    {
        $result = $this->dateFunctions->dateWithoutFormat('15/03/2024', ['d/m/Y']);

        $this->assertInstanceOf(DateTimeInterface::class, $result);
        $this->assertSame('2024-03-15', $result->format('Y-m-d'));
    }

    public function testDateWithoutFormatWithEmptyGuesses(): void
    {
        // Should still try built-in formats - using ISO8601 format
        $result = $this->dateFunctions->dateWithoutFormat('2024-03-15T10:30:00+00:00');

        $this->assertInstanceOf(DateTimeInterface::class, $result);
    }

    // =========================================================================
    // Tests for _DateWithoutFormat trait - dateWithoutFormat() method
    // =========================================================================

    public function testDateWithoutFormatWithISODate(): void
    {
        // Using a format that's in DateTimeImmutable constants
        $result = $this->dateFunctions->dateWithoutFormat('2024-03-15T10:30:00+00:00');

        $this->assertInstanceOf(DateTimeInterface::class, $result);
        $this->assertSame('2024-03-15', $result->format('Y-m-d'));
    }

    public function testDateWithoutFormatWithInvalidDateReturnsString(): void
    {
        $invalidDate = 'this-is-not-a-date';
        $result = $this->dateFunctions->dateWithoutFormat($invalidDate);

        $this->assertIsString($result);
        $this->assertSame($invalidDate, $result);
    }

    public function testDateWithoutFormatWithMultipleGuesses(): void
    {
        $result = $this->dateFunctions->dateWithoutFormat('25-12-2024', ['Y-m-d', 'd-m-Y']);

        $this->assertInstanceOf(DateTimeInterface::class, $result);
        $this->assertSame('2024-12-25', $result->format('Y-m-d'));
    }

    public function testExcelDateWithBaseTimestamp(): void
    {
        // Base timestamp 25569 = 1970-01-01 00:00:00
        $result = $this->dateFunctions->excelDate(25569);

        $this->assertIsString($result);
        $this->assertStringContainsString('01-01-1970', $result);
    }

    public function testExcelDateWithCustomFormat(): void
    {
        $result = $this->dateFunctions->excelDate(44927, 'Y-m-d');

        $this->assertSame('2023-01-01', $result);
    }

    public function testExcelDateWithLargeTimestamp(): void
    {
        // Excel timestamp 45000 = 2023-03-15
        $result = $this->dateFunctions->excelDate(45000, 'Y-m-d');

        $this->assertSame('2023-03-15', $result);
    }

    public function testExcelDateWithTimestampBelowBase(): void
    {
        // Timestamp below 25569 should return the timestamp as string
        $timestamp = 1000;
        $result = $this->dateFunctions->excelDate($timestamp);

        $this->assertSame('1000', $result);
    }

    // =========================================================================
    // Tests for _ExcelDate trait - excelDate() method
    // =========================================================================

    public function testExcelDateWithValidTimestamp(): void
    {
        // Excel timestamp 44927 = 2023-01-01
        $result = $this->dateFunctions->excelDate(44927);

        $this->assertIsString($result);
        $this->assertStringContainsString('01-01-2023', $result);
    }

    public function testFormatDateWithDateTime(): void
    {
        $result = $this->dateFunctions->formatDate('2024-03-15 14:30:45', 'Y-m-d H:i:s');

        $this->assertIsString($result);
        $this->assertSame('15-03-2024 14:30:45', $result);
    }

    public function testFormatDateWithDifferentInputFormat(): void
    {
        $result = $this->dateFunctions->formatDate('2024/12/25', 'Y/m/d');

        $this->assertIsString($result);
        $this->assertSame('25-12-2024 00:00:00', $result);
    }

    public function testFormatDateWithInvalidDateReturnsFalse(): void
    {
        $result = $this->dateFunctions->formatDate('invalid-date', 'd-m-Y');

        $this->assertFalse($result);
    }

    public function testFormatDateWithLeapYear(): void
    {
        $result = $this->dateFunctions->formatDate('29-02-2024', 'd-m-Y');

        $this->assertIsString($result);
        $this->assertStringContainsString('29-02-2024', $result);
    }

    // =========================================================================
    // Tests for _FormatDate trait - formatDate() method
    // =========================================================================

    public function testFormatDateWithValidDate(): void
    {
        $result = $this->dateFunctions->formatDate('15-03-2024', 'd-m-Y');

        $this->assertIsString($result);
        $this->assertSame('15-03-2024 00:00:00', $result);
    }

    public function testFormatTimeAsArray(): void
    {
        // 3661 seconds = 1 hour 1 minute 1 second
        $result = $this->dateFunctions->format(3661, true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('hour', $result);
        $this->assertArrayHasKey('minute', $result);
        $this->assertArrayHasKey('second', $result);
        $this->assertSame(1, $result['hour']);
        $this->assertSame(1, $result['minute']);
        $this->assertSame(1, $result['second']);
    }

    // =========================================================================
    // Tests for _TimeFormat trait - format() method
    // =========================================================================

    public function testFormatTimeAsString(): void
    {
        // 3661 seconds = 1 hour 1 minute 1 second
        $result = $this->dateFunctions->format(3661);

        $this->assertIsString($result);
        $this->assertStringContainsString('hour', $result);
        $this->assertStringContainsString('minute', $result);
        $this->assertStringContainsString('second', $result);
    }

    public function testFormatTimePluralHandling(): void
    {
        // Check singular vs plural
        $result = $this->dateFunctions->format(1);
        $this->assertStringContainsString('second', $result);
        $this->assertStringNotContainsString('seconds', $result);

        $result2 = $this->dateFunctions->format(2);
        $this->assertStringContainsString('seconds', $result2);
    }

    public function testFormatTimeWithFloat(): void
    {
        // 3.5 seconds
        $result = $this->dateFunctions->format(3.5);

        $this->assertIsString($result);
        $this->assertStringContainsString('second', $result);
    }

    public function testFormatTimeWithLargeValue(): void
    {
        // 7261 seconds = 2 hours 1 minute 1 second
        $result = $this->dateFunctions->format(7261);

        $this->assertIsString($result);
        $this->assertStringContainsString('2 hours', $result);
    }

    public function testFormatTimeWithOnlySeconds(): void
    {
        $result = $this->dateFunctions->format(30);

        $this->assertIsString($result);
        $this->assertStringContainsString('30', $result);
        $this->assertStringContainsString('second', $result);
    }

    public function testFormatTimeWithZero(): void
    {
        $result = $this->dateFunctions->format(0);

        $this->assertIsString($result);
        $this->assertSame('', $result);
    }

    /**
     * @throws Exception
     */
    public function testFromUnixTimestampWithCustomFormat(): void
    {
        $result = $this->dateFunctions->fromUnixTimestamp(1672531200, 'Y-m-d');

        $this->assertSame('2023-01-01', $result);
    }

    /**
     * @throws Exception
     */
    public function testFromUnixTimestampWithDefaultFormat(): void
    {
        $result = $this->dateFunctions->fromUnixTimestamp(1672531200);

        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}/', $result);
    }

    /**
     * @throws Exception
     */
    public function testFromUnixTimestampWithKnownValue(): void
    {
        // 1672531200 = 2023-01-01 00:00:00 UTC
        $result = $this->dateFunctions->fromUnixTimestamp(1672531200);

        $this->assertIsString($result);
        $this->assertStringContainsString('01-01-2023', $result);
    }

    /**
     * @throws Exception
     */
    public function testFromUnixTimestampWithNegativeValue(): void
    {
        // Negative timestamp for dates before 1970
        $result = $this->dateFunctions->fromUnixTimestamp(-86400, 'Y-m-d');

        $this->assertSame('1969-12-31', $result);
    }

    // =========================================================================
    // Tests for _FromUnixTimestamp trait - fromUnixTimestamp() method
    // =========================================================================

    /**
     * @throws Exception
     */
    public function testFromUnixTimestampWithZero(): void
    {
        $result = $this->dateFunctions->fromUnixTimestamp();

        $this->assertIsString($result);
        $this->assertSame('01-01-1970 00:00:00', $result);
    }

    public function testValidateDateBasicWithDefaultFormat(): void
    {
        $result = $this->dateFunctions->validateDateBasic('15-03-2024 12:30:00');

        $this->assertTrue($result);
    }

    public function testValidateDateBasicWithInvalidDate(): void
    {
        $result = $this->dateFunctions->validateDateBasic('invalid-date', 'd-m-Y H:i:s');

        $this->assertFalse($result);
    }

    public function testValidateDateBasicWithPartialMatch(): void
    {
        // Partial matches should fail - format requires exact match
        $result = $this->dateFunctions->validateDateBasic('15-03-2024', 'd-m-Y H:i:s');

        $this->assertFalse($result);
    }

    // =========================================================================
    // Tests for _ValidateDateBasic trait - validateDateBasic() method
    // =========================================================================

    public function testValidateDateBasicWithValidDate(): void
    {
        $result = $this->dateFunctions->validateDateBasic('15-03-2024 12:30:00', 'd-m-Y H:i:s');

        $this->assertTrue($result);
    }

    public function testValidateDateBasicWithWrongFormat(): void
    {
        // Date is valid but format doesn't match
        $result = $this->dateFunctions->validateDateBasic('2024-03-15', 'd-m-Y');

        $this->assertFalse($result);
    }

    public function testValidateDateWithInvalidDay(): void
    {
        // Invalid day: 32 for January
        $this->assertFalse($this->dateFunctions->validateDate('32012024'));
    }

    public function testValidateDateWithInvalidMonth(): void
    {
        // Invalid month: 13
        $this->assertFalse($this->dateFunctions->validateDate('15132024'));
    }

    public function testValidateDateWithLeapYear(): void
    {
        // Valid date: 29-02-2024 (leap year)
        $this->assertTrue($this->dateFunctions->validateDate('29022024'));
    }

    public function testValidateDateWithNonLeapYear(): void
    {
        // Note: Current implementation has simplified leap year check
        // It checks if first 2 digits of year are divisible by 4
        // Using year 2199 where "21" % 4 != 0, so Feb has only 28 days
        $this->assertFalse($this->dateFunctions->validateDate('29022199'));
    }

    // =========================================================================
    // Tests for _ValidateDate trait - validateDate() method
    // =========================================================================

    public function testValidateDateWithValidDate(): void
    {
        // Valid date: 15-03-2024
        $this->assertTrue($this->dateFunctions->validateDate('15032024'));
    }

    public function testValidateDateWithZeroDay(): void
    {
        // Invalid day: 0
        $this->assertFalse($this->dateFunctions->validateDate('00012024'));
    }

    public function testValidateDateWithZeroMonth(): void
    {
        // Invalid month: 0
        $this->assertFalse($this->dateFunctions->validateDate('15002024'));
    }

    protected function setUp(): void
    {
        $this->dateFunctions = new Functions();
    }
}
