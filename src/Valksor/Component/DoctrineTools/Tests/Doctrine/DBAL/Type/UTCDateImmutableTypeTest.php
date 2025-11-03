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
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidType;
use PHPUnit\Framework\TestCase;
use TypeError;
use Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateImmutableType;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

/**
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateImmutableType
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits\_ConvertToDatabaseValue
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits\_ConvertToPHPValue
 */
final class UTCDateImmutableTypeTest extends TestCase
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

        $type = new UTCDateImmutableType();
        $input = new DateTimeImmutable('2024-01-01 22:15:30', new DateTimeZone('America/New_York'));

        $result = $type->convertToDatabaseValue($input, $platform);

        self::assertSame('2024-01-02', $result);
    }

    /**
     * @throws ConversionException
     */
    public function testConvertToPhpValueCreatesUtcImmutable(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $platform
            ->method('getDateTimeFormatString')
            ->willReturn('Y-m-d H:i:s');

        $result = new UTCDateImmutableType()->convertToPHPValue('2024-01-02 00:00:00', $platform);

        self::assertInstanceOf(UTCDateTimeImmutable::class, $result);
        self::assertSame('2024-01-02 00:00:00.000000', $result->format(UTCDateTimeImmutable::FORMAT));
        self::assertSame('UTC', $result->getTimezone()->getName());
    }

    /**
     * @throws ConversionException
     */
    public function testConvertToPhpValueRejectsInvalidFormat(): void
    {
        $this->expectException(TypeError::class);

        $platform = $this->createMock(AbstractPlatform::class);
        $platform
            ->method('getDateTimeFormatString')
            ->willReturn('Y-m-d H:i:s');

        $type = new UTCDateImmutableType();
        $type->convertToPHPValue('not-a-date', $platform);
    }

    protected function tearDown(): void
    {
        UTCDateTimeImmutable::$timezone = null;
    }
}
