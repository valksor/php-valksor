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

namespace Valksor\Component\DoctrineTools\Doctrine\DBAL\Type;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

class UTCDateTimeImmutableType extends DateTimeImmutableType
{
    use Traits\_ConvertToDatabaseValue;
    use Traits\_ConvertToPHPValue;

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(
        $value,
        AbstractPlatform $platform,
    ): ?DateTimeImmutable {
        return $this->convertToPHPValueForType(value: $value, platform: $platform, object: new UTCDateTimeImmutable(), function: 'date_create_immutable');
    }
}
