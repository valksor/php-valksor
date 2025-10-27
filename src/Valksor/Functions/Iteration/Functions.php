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

namespace Valksor\Functions\Iteration;

final class Functions
{
    use Traits\_AddElementIfNotExists;
    use Traits\_ArrayFlipRecursive;
    use Traits\_ArrayIntersectKeyRecursive;
    use Traits\_ArrayToString;
    use Traits\_ArrayValuesFiltered;
    use Traits\_FilterKeyEndsWith;
    use Traits\_FilterKeyStartsWith;
    use Traits\_FirstMatchAsString;
    use Traits\_HaveCommonElements;
    use Traits\_IsAssociative;
    use Traits\_IsEmpty;
    use Traits\_IsMultiDimentional;
    use Traits\_IsSortable;
    use Traits\_IsSortedAscendingInts;
    use Traits\_JsonDecode;
    use Traits\_JsonEncode;
    use Traits\_MakeMultiDimensional;
    use Traits\_MakeOneDimension;
    use Traits\_Pick;
    use Traits\_RecursiveKSort;
    use Traits\_RemoveFromArray;
    use Traits\_SwapArray;
    use Traits\_Unique;
    use Traits\_UniqueMap;
    use Traits\_Unpack;
}
