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

namespace Valksor\Functions\Sort;

final class Functions
{
    use Traits\_BubbleSort;
    use Traits\_MergeSort;
    use Traits\_SortByParameter;
    use Traits\_StableSort;
    use Traits\_Usort;
}
