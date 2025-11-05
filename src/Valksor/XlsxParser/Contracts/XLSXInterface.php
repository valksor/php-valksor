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

namespace Valksor\XlsxParser\Contracts;

use Iterator;

interface XLSXInterface
{
    public function getIndex(
        string $name,
    ): int;

    public function getRows(
        int $index,
    ): Iterator;

    public function getWorksheets(): array;
}
