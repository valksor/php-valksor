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

use function implode;
use function is_array;
use function trim;

/**
 * @internal
 */
abstract class AbstractXMLDictionary extends AbstractXMLResource
{
    protected bool $valid = true;
    protected array $values = [];

    public function get(
        int $index,
    ): mixed {
        while ($this->valid && !isset($this->values[$index])) {
            $this->readNext();
        }

        if (is_array($this->values[$index])) {
            return trim(implode(' ', $this->values[$index]));
        }

        return $this->values[$index];
    }

    abstract protected function readNext();
}
