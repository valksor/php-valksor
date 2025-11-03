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

namespace Valksor\Component\DoctrineTools\Tests\Normalizer;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Valksor\Component\DoctrineTools\Normalizer\UTCDateTimeNormalizer;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

/**
 * @covers \Valksor\Component\DoctrineTools\Normalizer\UTCDateTimeNormalizer
 */
final class UTCDateTimeNormalizerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testDenormalizeCreatesUtcInstance(): void
    {
        $result = new UTCDateTimeNormalizer()->denormalize('2024-01-01T15:00:00+02:00', UTCDateTimeImmutable::class);

        self::assertInstanceOf(UTCDateTimeImmutable::class, $result);
        self::assertSame('2024-01-01 15:00:00.000000', $result->format(UTCDateTimeImmutable::FORMAT));
        self::assertSame('+02:00', $result->getTimezone()->getName());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeUsesFormatFromContext(): void
    {
        $normalizer = new UTCDateTimeNormalizer();
        $value = new UTCDateTimeImmutable('2024-01-01 10:00:00');

        $result = $normalizer->normalize(
            $value,
            null,
            ['datetime_format' => DateTimeInterface::RFC3339_EXTENDED],
        );

        self::assertSame('2024-01-01T10:00:00.000+00:00', $result);
    }

    public function testSupportedTypesExposeUtcDateTimeImmutable(): void
    {
        $supported = new UTCDateTimeNormalizer()->getSupportedTypes(null);

        self::assertArrayHasKey(UTCDateTimeImmutable::class, $supported);
        self::assertTrue($supported[UTCDateTimeImmutable::class]);
    }

    public function testSupportsDenormalizationOnlyForUtcDateTimeImmutable(): void
    {
        $normalizer = new UTCDateTimeNormalizer();

        self::assertTrue($normalizer->supportsDenormalization('value', UTCDateTimeImmutable::class));
        self::assertFalse($normalizer->supportsDenormalization('value', DateTimeImmutable::class));
    }

    public function testSupportsNormalizationOnlyForUtcDateTimeImmutable(): void
    {
        $normalizer = new UTCDateTimeNormalizer();

        self::assertTrue($normalizer->supportsNormalization(new UTCDateTimeImmutable()));
        self::assertFalse($normalizer->supportsNormalization(new DateTimeImmutable()));
    }
}
