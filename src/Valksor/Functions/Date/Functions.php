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

namespace Valksor\Functions\Date;

final class Functions
{
    use Traits\_Date;
    use Traits\_DateNullable;
    use Traits\_DateWithoutFormat;
    use Traits\_ExcelDate;
    use Traits\_FormatDate;
    use Traits\_FromUnixTimestamp;
    use Traits\_TimeFormat;
    use Traits\_ValidateDate;
    use Traits\_ValidateDateBasic;

    public const array EXTRA_FORMATS = [
        self::FORMAT,
        self::FORMAT_TS,
    ];
    public const string FORMAT = 'd-m-Y H:i:s';
    public const string FORMAT_TS = 'D M d Y H:i:s T';
    public const int HOUR = 60 * self::MIN;
    public const int MAY = 31;
    public const int MIN = 60 * self::SEC;
    public const int MS = 1;
    public const int SEC = 1000 * self::MS;
    public const array TIME = [
        'hour' => self::HOUR,
        'minute' => self::MIN,
        'second' => self::SEC,
        'micro' => self::MS,
    ];
}
