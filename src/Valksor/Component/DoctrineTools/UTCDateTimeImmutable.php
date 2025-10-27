<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Dāvis Zālītis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Component\DoctrineTools;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

class UTCDateTimeImmutable extends DateTimeImmutable
{
    public const string FORMAT = 'Y-m-d H:i:s.u';
    public const string UTC = 'UTC';
    public static ?DateTimeZone $timezone = null;

    /**
     * @throws Exception
     */
    public function __construct(
        string $datetime = 'now',
    ) {
        self::$timezone = self::getUTCTimeZone();

        parent::__construct($datetime, self::$timezone);
    }

    /**
     * @throws Exception
     */
    public static function createFromFormat(
        string $format,
        string $datetime,
        ?DateTimeZone $timezone = null,
    ): static|false {
        $object = parent::createFromFormat($format, $datetime, $timezone ?? self::getUTCTimeZone());

        if (false !== $object || false !== self::getLastErrors()) {
            return self::createFromInterface($object);
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public static function createFromInterface(
        DateTimeInterface $object,
    ): static {
        return new static($object->setTimezone(self::getUTCTimeZone())->format(self::FORMAT));
    }

    public static function getUTCTimeZone(): DateTimeZone
    {
        return self::$timezone ?? new DateTimeZone(self::UTC);
    }
}
