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

namespace Valksor\Component\DoctrineTools\Doctrine\DBAL\Traits;

use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Valksor\Component\DoctrineTools\UTCDateTimeImmutable;

trait _ConvertToDatabaseValue
{
    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform,
    ): ?string {
        if ($value instanceof DateTimeInterface) {
            $value = $value->setTimezone(timezone: UTCDateTimeImmutable::getUTCTimeZone());
        }

        return parent::convertToDatabaseValue(value: $value, platform: $platform);
    }
}
