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

namespace Valksor\XlsxParser\Xlsx;

use function count;

/**
 * @internal
 */
final class Row
{
    private array $values = [];

    public function addValue(
        int $columnIndex,
        mixed $value,
    ): void {
        if ('' !== $value) {
            $this->values[$columnIndex] = $value;
        }
    }

    public function getData(): array
    {
        $data = [];

        foreach ($this->values as $columnIndex => $value) {
            while (count($data) < $columnIndex) {
                $data[] = '';
            }

            $data[] = $value;
        }

        return $data;
    }
}
