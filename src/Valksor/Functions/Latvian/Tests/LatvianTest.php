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

namespace Valksor\Functions\Latvian\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Valksor\Functions\Latvian\Functions;

final class LatvianTest extends TestCase
{
    private Functions $latvian;

    public function testCompareWithArrayValues(): void
    {
        $first = ['name' => 'Ābols'];
        $second = ['name' => 'Bērzs'];

        self::assertLessThan(0, $this->latvian->compare($first, $second, 'name'));
    }

    public function testCompareWithObjectValues(): void
    {
        $first = new stdClass();
        $first->label = 'Čiekurs';
        $second = new stdClass();
        $second->label = 'Žanis';

        self::assertLessThan(0, $this->latvian->compare($first, $second, 'label'));
    }

    public function testSortLatvianSupportsCustomCallback(): void
    {
        $names = [
            ['name' => 'Ādams'],
            ['name' => 'Biruta'],
            ['name' => 'Česlavs'],
        ];

        $comparator = new class($this->latvian) {
            public function __construct(
                private readonly Functions $latvian,
            ) {
            }

            public function compare(
                array $a,
                array $b,
                string $field,
            ): int {
                return -$this->latvian->compare($a, $b, $field);
            }
        };

        $result = $this->latvian->sortLatvian(
            $names,
            'name',
            [$comparator, 'compare'],
        );

        self::assertTrue($result);
        self::assertSame(
            ['Česlavs', 'Biruta', 'Ādams'],
            array_column($names, 'name'),
        );
    }

    public function testSortLatvianUsesDefaultComparator(): void
    {
        $names = [
            ['value' => 'Žanis'],
            ['value' => 'Ādams'],
            ['value' => 'Česlavs'],
            ['value' => 'Biruta'],
        ];

        $result = $this->latvian->sortLatvian($names, 'value');

        self::assertTrue($result);
        self::assertSame(
            ['Ādams', 'Biruta', 'Česlavs', 'Žanis'],
            array_column($names, 'value'),
        );
    }

    public function testValidatePersonCodeAcceptsOldCodeValidByDateFallback(): void
    {
        // Invalid old checksum, but a real birth date (17 Feb) accepts it via the date fallback.
        self::assertTrue($this->latvian->validatePersonCode('17020692090'));
    }

    public function testValidatePersonCodeAcceptsValidNewCode(): void
    {
        self::assertTrue($this->latvian->validatePersonCode('32990268037'));
    }

    public function testValidatePersonCodeAcceptsValidOldCode(): void
    {
        self::assertTrue($this->latvian->validatePersonCode('17020692093'));
    }

    public function testValidatePersonCodeNewAcceptsFormattedInput(): void
    {
        self::assertTrue($this->latvian->validatePersonCodeNew('32 99026-8037'));
    }

    public function testValidatePersonCodeNewHandlesDirectChecksum(): void
    {
        self::assertTrue($this->latvian->validatePersonCodeNew('32583005221'));
    }

    public function testValidatePersonCodeNewHandlesWrappedChecksum(): void
    {
        self::assertTrue($this->latvian->validatePersonCodeNew('32990268037'));
    }

    public function testValidatePersonCodeNewRejectsInvalidChecksum(): void
    {
        self::assertFalse($this->latvian->validatePersonCodeNew('32990268030'));
    }

    public function testValidatePersonCodeNewThrowsForInvalidLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->latvian->validatePersonCodeNew('1234567890');
    }

    public function testValidatePersonCodeOldRejectsInvalid(): void
    {
        self::assertFalse($this->latvian->validatePersonCodeOld('17020692090'));
    }

    public function testValidatePersonCodeOldValid(): void
    {
        self::assertTrue($this->latvian->validatePersonCodeOld('17020692093'));
    }

    public function testValidatePersonCodeRejectsInvalidNewCode(): void
    {
        self::assertFalse($this->latvian->validatePersonCode('32990268030'));
    }

    public function testValidatePersonCodeRejectsOldCodeWithInvalidChecksumAndDate(): void
    {
        // Not a new-format code, invalid old checksum, and an impossible date (day/month 00).
        self::assertFalse($this->latvian->validatePersonCode('00000000000'));
    }

    protected function setUp(): void
    {
        $this->latvian = new Functions();
    }
}
