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

namespace Valksor\Functions\Number;

final class Functions
{
    use Traits\_DistanceBetweenPoints;
    use Traits\_DistanceInKm;
    use Traits\_GreatestCommonDiviser;
    use Traits\_IsFloat;
    use Traits\_IsInt;
    use Traits\_IsPrimal;
    use Traits\_IsPrime;
    use Traits\_IsPrimeBelow1000;
    use Traits\_IsPrimeGmp;
    use Traits\_LeastCommonMultiple;
    use Traits\_Swap;
}
