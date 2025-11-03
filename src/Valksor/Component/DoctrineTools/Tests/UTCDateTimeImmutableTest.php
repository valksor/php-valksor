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

namespace Valksor\Component\DoctrineTools\Tests;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

/**
 * @covers \Valksor\Component\DoctrineTools\UTCDateTimeImmutable
 */
final class UTCDateTimeImmutableTest extends TestCase
{
    public function testConstructorInitializesUtcTimezone(): void
    {
        $dateTime = new UTCDateTimeImmutable('2024-01-01 12:34:56');

        self::assertSame('UTC', $dateTime->getTimezone()->getName());
        self::assertSame('2024-01-01 12:34:56.000000', $dateTime->format(UTCDateTimeImmutable::FORMAT));
    }

    /**
     * @throws Exception
     */
    public function testCreateFromFormatNormalizesTimezone(): void
    {
        $result = UTCDateTimeImmutable::createFromFormat(
            'Y-m-d H:i:sP',
            '2024-01-01 15:00:00+02:00',
            new DateTimeZone('Europe/Riga'),
        );

        self::assertInstanceOf(UTCDateTimeImmutable::class, $result);
        self::assertSame('UTC', $result->getTimezone()->getName());
        self::assertSame('2024-01-01 13:00:00.000000', $result->format(UTCDateTimeImmutable::FORMAT));
    }

    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function testCreateFromInterfaceReturnsUtcInstance(): void
    {
        $source = new DateTimeImmutable('2024-01-01 15:00:00', new DateTimeZone('America/New_York'));

        $result = UTCDateTimeImmutable::createFromInterface($source);

        self::assertSame('UTC', $result->getTimezone()->getName());
        self::assertSame('2024-01-01 20:00:00.000000', $result->format(UTCDateTimeImmutable::FORMAT));
    }

    protected function tearDown(): void
    {
        UTCDateTimeImmutable::$timezone = null;
    }
}
