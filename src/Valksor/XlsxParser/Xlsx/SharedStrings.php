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

use XMLReader;

use function strtr;
use function trim;

/**
 * @internal
 */
final class SharedStrings extends AbstractXMLDictionary
{
    private const string INDEX = 'si';
    private const string VALUE = 't';

    private int $currentIndex = -1;

    protected function readNext(): void
    {
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType) {
                $this->process($xml);
            }
        }

        $this->valid = false;
        $this->closeXMLReader();
    }

    private function process(
        XMLReader $xml,
    ): void {
        match ($xml->name) {
            self::INDEX => $this->currentIndex++,
            self::VALUE => $this->values[$this->currentIndex][] = trim(strtr($xml->readString(), ["\u{a0}" => ' ']), ' '),
            default => null,
        };
    }
}
