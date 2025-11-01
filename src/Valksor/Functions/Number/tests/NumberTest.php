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

namespace Valksor\Functions\Number\Tests;

use PHPUnit\Framework\TestCase;
use Valksor\Functions\Number\Functions;

final class NumberTest extends TestCase
{
    private Functions $number;

    // distanceBetweenPoints() tests

    public function testDistanceBetweenPointsForSameLocation(): void
    {
        $distance = $this->number->distanceBetweenPoints(40.7128, -74.0060, 40.7128, -74.0060);
        $this->assertSame(0.0, $distance);
    }

    public function testDistanceBetweenPointsInKilometers(): void
    {
        // New York to Los Angeles
        $distance = $this->number->distanceBetweenPoints(40.7128, -74.0060, 34.0522, -118.2437, true, 2);
        $this->assertGreaterThan(3900.0, $distance);
        $this->assertLessThan(4000.0, $distance);
    }

    public function testDistanceBetweenPointsInMiles(): void
    {
        // New York to Los Angeles
        $distance = $this->number->distanceBetweenPoints(40.7128, -74.0060, 34.0522, -118.2437, false, 2);
        $this->assertGreaterThan(2400.0, $distance);
        $this->assertLessThan(2500.0, $distance);
    }

    public function testDistanceBetweenPointsShortDistance(): void
    {
        // Two close points
        $distance = $this->number->distanceBetweenPoints(40.7128, -74.0060, 40.7589, -73.9851, true, 2);
        $this->assertGreaterThan(5.0, $distance);
        $this->assertLessThan(10.0, $distance);
    }

    public function testDistanceBetweenPointsWithDifferentPrecision(): void
    {
        $distance2 = $this->number->distanceBetweenPoints(51.5074, -0.1278, 48.8566, 2.3522, true, 2);
        $distance4 = $this->number->distanceBetweenPoints(51.5074, -0.1278, 48.8566, 2.3522);

        $this->assertIsFloat($distance2);
        $this->assertIsFloat($distance4);
        $this->assertGreaterThan(330.0, $distance2);
        $this->assertLessThan(350.0, $distance2);
    }

    // distanceInKm() tests

    public function testDistanceInKmBetweenTwoCities(): void
    {
        // London to Paris
        $distance = $this->number->distanceInKm(51.5074, -0.1278, 48.8566, 2.3522);
        $this->assertGreaterThan(330.0, $distance);
        $this->assertLessThan(350.0, $distance);
    }

    public function testDistanceInKmForLongDistance(): void
    {
        // New York to Tokyo
        $distance = $this->number->distanceInKm(40.7128, -74.0060, 35.6762, 139.6503);
        $this->assertGreaterThan(10000.0, $distance);
        $this->assertLessThan(11000.0, $distance);
    }

    public function testDistanceInKmForSameLocation(): void
    {
        $distance = $this->number->distanceInKm(40.7128, -74.0060, 40.7128, -74.0060);
        $this->assertSame(0.0, $distance);
    }

    public function testDistanceInKmReturnsFloat(): void
    {
        $distance = $this->number->distanceInKm(51.5074, -0.1278, 48.8566, 2.3522);
        $this->assertIsFloat($distance);
    }

    // greatestCommonDivisor() tests

    public function testGreatestCommonDivisorWithCommonDivisor(): void
    {
        $this->assertSame(6, $this->number->greatestCommonDivisor(48, 18));
        $this->assertSame(5, $this->number->greatestCommonDivisor(15, 20));
    }

    public function testGreatestCommonDivisorWithCoprimeNumbers(): void
    {
        $this->assertSame(1, $this->number->greatestCommonDivisor(17, 19));
        $this->assertSame(1, $this->number->greatestCommonDivisor(13, 7));
    }

    public function testGreatestCommonDivisorWithLargeNumbers(): void
    {
        $this->assertSame(120, $this->number->greatestCommonDivisor(1200, 360));
        $this->assertSame(25, $this->number->greatestCommonDivisor(100, 75));
    }

    public function testGreatestCommonDivisorWithNegativeNumbers(): void
    {
        $this->assertSame(6, $this->number->greatestCommonDivisor(-48, 18));
        $this->assertSame(6, $this->number->greatestCommonDivisor(48, -18));
        $this->assertSame(6, $this->number->greatestCommonDivisor(-48, -18));
    }

    public function testGreatestCommonDivisorWithSameNumber(): void
    {
        $this->assertSame(42, $this->number->greatestCommonDivisor(42, 42));
    }

    public function testGreatestCommonDivisorWithZero(): void
    {
        $this->assertSame(5, $this->number->greatestCommonDivisor(5, 0));
        $this->assertSame(10, $this->number->greatestCommonDivisor(0, 10));
    }

    public function testIsFloatAcceptsActualFloats(): void
    {
        $this->assertTrue($this->number->isFloat(123.45));
        $this->assertTrue($this->number->isFloat(-456.789));
    }

    public function testIsFloatReturnsFalseForIntegerStrings(): void
    {
        $this->assertFalse($this->number->isFloat('123'));
        $this->assertFalse($this->number->isFloat('0'));
    }

    public function testIsFloatReturnsFalseForNonNumeric(): void
    {
        $this->assertFalse($this->number->isFloat('abc'));
        $this->assertFalse($this->number->isFloat('12.34a'));
    }

    // isFloat() tests

    public function testIsFloatReturnsTrueForValidFloats(): void
    {
        $this->assertTrue($this->number->isFloat('123.45'));
        $this->assertTrue($this->number->isFloat('0.0'));
        $this->assertTrue($this->number->isFloat('-456.789'));
    }

    public function testIsIntAcceptsActualIntegers(): void
    {
        $this->assertTrue($this->number->isInt(123));
        $this->assertTrue($this->number->isInt(0));
    }

    public function testIsIntReturnsFalseForFloatStrings(): void
    {
        $this->assertFalse($this->number->isInt('123.45'));
        $this->assertFalse($this->number->isInt('0.0'));
    }

    public function testIsIntReturnsFalseForNegativeStrings(): void
    {
        $this->assertFalse($this->number->isInt('-123'));
    }

    public function testIsIntReturnsFalseForNonNumericStrings(): void
    {
        $this->assertFalse($this->number->isInt('abc'));
        $this->assertFalse($this->number->isInt('12a'));
        $this->assertFalse($this->number->isInt(''));
    }

    // isInt() tests

    public function testIsIntReturnsTrueForValidIntegers(): void
    {
        $this->assertTrue($this->number->isInt('123'));
        $this->assertTrue($this->number->isInt('0'));
        $this->assertTrue($this->number->isInt('456'));
    }

    public function testIsPrimeForLargeComposite(): void
    {
        $this->assertFalse($this->number->isPrime(100));
        $this->assertFalse($this->number->isPrime(1001));
    }

    public function testIsPrimeForLargePrimes(): void
    {
        $this->assertTrue($this->number->isPrime(97));
        $this->assertTrue($this->number->isPrime(997));
        $this->assertFalse($this->number->isPrime(1000));
    }

    // isPrimeGmp() tests

    public function testIsPrimeGmpReturnsNullWhenOverrideIsTrue(): void
    {
        // When override is true, method should return null regardless of GMP availability
        $result = $this->number->isPrimeGmp(17, true);
        $this->assertNull($result);
    }

    public function testIsPrimeGmpWithGmpAvailable(): void
    {
        // When GMP is available and override is false, should return boolean or null based on gmp_prob_prime result
        $result = $this->number->isPrimeGmp(17);
        $this->assertIsBool($result);

        $result2 = $this->number->isPrimeGmp(18);
        $this->assertIsBool($result2);
    }

    public function testIsPrimeReturnsFalseForCompositeNumbers(): void
    {
        $this->assertFalse($this->number->isPrime(4));
        $this->assertFalse($this->number->isPrime(6));
        $this->assertFalse($this->number->isPrime(8));
        $this->assertFalse($this->number->isPrime(9));
        $this->assertFalse($this->number->isPrime(10));
    }

    public function testIsPrimeReturnsFalseForNegativeNumbers(): void
    {
        $this->assertFalse($this->number->isPrime(-1));
        $this->assertFalse($this->number->isPrime(-5));
        $this->assertFalse($this->number->isPrime(-17));
    }

    public function testIsPrimeReturnsFalseForZeroAndOne(): void
    {
        $this->assertFalse($this->number->isPrime(0));
        $this->assertFalse($this->number->isPrime(1));
    }

    // isPrime() tests

    public function testIsPrimeReturnsTrueForSmallPrimes(): void
    {
        $this->assertTrue($this->number->isPrime(2));
        $this->assertTrue($this->number->isPrime(3));
        $this->assertTrue($this->number->isPrime(5));
        $this->assertTrue($this->number->isPrime(7));
        $this->assertTrue($this->number->isPrime(11));
    }

    public function testLeastCommonMultipleWithCoprimeNumbers(): void
    {
        $this->assertSame(323, $this->number->leastCommonMultiple(17, 19));
    }

    public function testLeastCommonMultipleWithNegativeNumbers(): void
    {
        $this->assertSame(60, $this->number->leastCommonMultiple(-12, 15));
        $this->assertSame(60, $this->number->leastCommonMultiple(12, -15));
    }

    // leastCommonMultiple() tests

    public function testLeastCommonMultipleWithNormalCases(): void
    {
        $this->assertSame(60, $this->number->leastCommonMultiple(12, 15));
        $this->assertSame(24, $this->number->leastCommonMultiple(8, 6));
    }

    public function testLeastCommonMultipleWithSameNumber(): void
    {
        $this->assertSame(42, $this->number->leastCommonMultiple(42, 42));
    }

    public function testLeastCommonMultipleWithZero(): void
    {
        $this->assertSame(0, $this->number->leastCommonMultiple(5, 0));
        $this->assertSame(0, $this->number->leastCommonMultiple(0, 10));
        $this->assertSame(0, $this->number->leastCommonMultiple(0, 0));
    }

    // Integration tests

    public function testMultipleOperationsOnSameInstance(): void
    {
        $this->assertTrue($this->number->isPrime(7));
        $this->assertSame(6, $this->number->greatestCommonDivisor(48, 18));
        $this->assertTrue($this->number->isInt('123'));

        $a = 5;
        $b = 10;
        $this->number->swap($a, $b);
        $this->assertSame(10, $a);
    }

    public function testStaticHelperCreation(): void
    {
        // Test that static helpers are created and reused
        $number1 = new Functions();
        $number2 = new Functions();

        $this->assertTrue($number1->isPrime(17));
        $this->assertTrue($number2->isPrime(19));
    }

    public function testSwapByReference(): void
    {
        $x = 1;
        $y = 2;
        $this->number->swap($x, $y);
        $this->assertSame(2, $x);
        $this->assertSame(1, $y);
    }

    // swap() tests

    public function testSwapExchangesTwoValues(): void
    {
        $a = 5;
        $b = 10;
        $this->number->swap($a, $b);
        $this->assertSame(10, $a);
        $this->assertSame(5, $b);
    }

    public function testSwapWithDifferentTypes(): void
    {
        $a = 'hello';
        $b = 42;
        $this->number->swap($a, $b);
        $this->assertSame(42, $a);
        $this->assertSame('hello', $b);
    }

    public function testSwapWithSameValues(): void
    {
        $a = 5;
        $b = 5;
        $this->number->swap($a, $b);
        $this->assertSame(5, $a);
        $this->assertSame(5, $b);
    }

    public function testSwapWithStrings(): void
    {
        $a = 'foo';
        $b = 'bar';
        $this->number->swap($a, $b);
        $this->assertSame('bar', $a);
        $this->assertSame('foo', $b);
    }

    protected function setUp(): void
    {
        $this->number = new Functions();
    }
}
