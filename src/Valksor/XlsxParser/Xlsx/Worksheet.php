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

/**
 * @internal
 */
final class Worksheet extends AbstractXMLResource
{
    private const string ID = 'id';
    private const string NAME = 'name';
    private const string SHEET = 'sheet';

    public function getWorksheetPaths(
        Relationships $relationships,
    ): array {
        $xml = $this->getXMLReader();
        $paths = [];

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType && self::SHEET === $xml->name) {
                $rId = $xml->getAttributeNs(self::ID, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                $paths[$xml->getAttribute(self::NAME)] = $relationships->getWorksheetPath($rId);
            }
        }

        return $paths;
    }
}
