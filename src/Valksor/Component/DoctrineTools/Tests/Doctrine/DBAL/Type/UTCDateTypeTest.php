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

namespace Valksor\Component\DoctrineTools\Tests\Doctrine\DBAL\Type;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use PHPUnit\Framework\TestCase;
use Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateType;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

/**
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateType
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits\_ConvertToDatabaseValue
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits\_ConvertToPHPValue
 */
final class UTCDateTypeTest extends TestCase
{
    /**
     * @throws InvalidType
     * @throws DateMalformedStringException
     */
    public function testConvertToDatabaseValueNormalizesTimezone(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $platform
            ->method('getDateFormatString')
            ->willReturn('Y-m-d');

        $type = new UTCDateType();
        $input = new DateTime('2024-01-01 22:15:30', new DateTimeZone('America/New_York'));

        $result = $type->convertToDatabaseValue($input, $platform);

        self::assertSame('2024-01-02', $result);
    }

    /**
     * @throws ConversionException
     */
    public function testConvertToPhpValueCreatesUtcDateTime(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $platform
            ->method('getDateTimeFormatString')
            ->willReturn('Y-m-d H:i:s');

        $result = new UTCDateType()->convertToPHPValue('2024-01-02 00:00:00', $platform);

        self::assertInstanceOf(DateTime::class, $result);
        self::assertSame('UTC', $result->getTimezone()->getName());
        self::assertSame('2024-01-02 00:00:00', $result->format('Y-m-d H:i:s'));
    }

    /**
     * @throws ConversionException
     */
    public function testConvertToPhpValueRejectsInvalidFormat(): void
    {
        $this->expectException(InvalidFormat::class);

        $platform = $this->createMock(AbstractPlatform::class);
        $platform
            ->method('getDateTimeFormatString')
            ->willReturn('Y-m-d H:i:s');

        $type = new UTCDateType();
        $type->convertToPHPValue('not-a-date', $platform);
    }

    protected function tearDown(): void
    {
        UTCDateTimeImmutable::$timezone = null;
    }
}
