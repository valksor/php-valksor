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
use Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateTimeImmutableType;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

/**
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateTimeImmutableType
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits\_ConvertToDatabaseValue
 * @covers \Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits\_ConvertToPHPValue
 */
final class UTCDateTimeImmutableTypeTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     * @throws InvalidType
     */
    public function testConvertToDatabaseValueNormalizesTimezone(): void
    {
        $platform = $this->createStub(AbstractPlatform::class);
        $platform
            ->method('getDateTimeFormatString')
            ->willReturn('Y-m-d H:i:s');

        $input = new DateTimeImmutable('2024-01-01 10:15:30', new DateTimeZone('America/New_York'));

        $result = new UTCDateTimeImmutableType()->convertToDatabaseValue($input, $platform);

        $expected = $input->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');

        self::assertSame($expected, $result);
    }

    /**
     * @throws ConversionException
     */
    public function testConvertToPhpValueReturnsUtcImmutable(): void
    {
        $platform = $this->createStub(AbstractPlatform::class);
        $platform
            ->method('getDateTimeFormatString')
            ->willReturn('Y-m-d H:i:s');

        $result = new UTCDateTimeImmutableType()->convertToPHPValue('2024-01-01 17:00:00', $platform);

        self::assertInstanceOf(UTCDateTimeImmutable::class, $result);
        self::assertSame('UTC', $result->getTimezone()->getName());
        self::assertSame('2024-01-01 17:00:00.000000', $result->format(UTCDateTimeImmutable::FORMAT));
    }

    protected function tearDown(): void
    {
        UTCDateTimeImmutable::$timezone = null;
    }
}
