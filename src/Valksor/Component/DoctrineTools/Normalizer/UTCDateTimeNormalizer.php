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

namespace Valksor\Component\DoctrineTools\Normalizer;

use DateTimeInterface;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

#[Autoconfigure(lazy: true)]
class UTCDateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @throws Exception
     */
    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): ?UTCDateTimeImmutable {
        return new UTCDateTimeImmutable($data);
    }

    public function getSupportedTypes(
        ?string $format,
    ): array {
        return [
            UTCDateTimeImmutable::class => true,
        ];
    }

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): string {
        return $data->format($context['datetime_format'] ?? DateTimeInterface::RFC3339);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return UTCDateTimeImmutable::class === $type;
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $data instanceof UTCDateTimeImmutable;
    }
}
