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

namespace Valksor\Functions\Latvian;

final class Functions
{
    use Traits\_Compare;
    use Traits\_SortLatvian;
    use Traits\_ValidatePersonCode;
    use Traits\_ValidatePersonCodeNew;
    use Traits\_ValidatePersonCodeOld;
}
