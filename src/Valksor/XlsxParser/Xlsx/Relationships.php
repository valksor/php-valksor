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

use function basename;

/**
 * @internal
 */
final class Relationships extends AbstractXMLResource
{
    private const string ID = 'Id';
    private const string RELATIONSHIP = 'Relationship';
    private const string SHARED_STRINGS = 'sharedStrings';
    private const string STYLES = 'styles';
    private const string TARGET = 'Target';
    private const string TYPE = 'Type';
    private const string WORKSHEET = 'worksheet';
    private string $sharedStringPath = '';
    private string $stylePath = '';

    private array $workSheetPaths = [];

    public function __construct(
        string $path,
    ) {
        parent::__construct($path);
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType && self::RELATIONSHIP === $xml->name) {
                $target = 'xl/' . $xml->getAttribute(self::TARGET);

                match (basename((string) $xml->getAttribute(self::TYPE))) {
                    self::WORKSHEET => $this->workSheetPaths[$xml->getAttribute(self::ID)] = $target,
                    self::STYLES => $this->stylePath = $target,
                    self::SHARED_STRINGS => $this->sharedStringPath = $target,
                    default => null,
                };
            }
        }

        $this->closeXMLReader();
    }

    public function getSharedStringsPath(): string
    {
        return $this->sharedStringPath;
    }

    public function getStylesPath(): string
    {
        return $this->stylePath;
    }

    public function getWorksheetPath(
        string $rId,
    ): string {
        return $this->workSheetPaths[$rId];
    }
}
