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

use Iterator;
use Valksor\XlsxParser\Contracts;
use Valksor\XlsxParser\Exception\InvalidIndexException;
use Valksor\XlsxParser\Transformer;

use function array_keys;
use function array_search;
use function array_values;
use function is_int;

/**
 * @internal
 */
final class XLSX implements Contracts\XLSXInterface
{
    private ?Relationships $relationships = null;
    private ?SharedStrings $sharedStrings = null;
    private ?Styles $styles = null;
    private ?Transformer\Value $valueTransformer = null;
    private ?array $worksheetPaths = null;

    public function __construct(
        private readonly Archive $archive,
    ) {
    }

    public function getIndex(
        string $name,
    ): int {
        $result = array_search($name, $this->getWorksheets(), true);

        return match (is_int($result)) {
            true => $result,
            default => throw new InvalidIndexException($name),
        };
    }

    public function getRows(
        int $index,
    ): Iterator {
        return new RowIterator($this->getValueTransformer(), $this->archive->extract(array_values($this->getWorksheetPaths())[$index]), );
    }

    public function getWorksheets(): array
    {
        return array_keys($this->getWorksheetPaths());
    }

    private function getRelationships(): Relationships
    {
        return $this->relationships ??= new Relationships($this->archive->extract('xl/_rels/workbook.xml.rels'));
    }

    private function getSharedStrings(): SharedStrings
    {
        return $this->sharedStrings ??= new SharedStrings($this->archive->extract($this->getRelationships()->getSharedStringsPath()));
    }

    private function getStyles(): Styles
    {
        return $this->styles ??= new Styles($this->archive->extract($this->getRelationships()->getStylesPath()));
    }

    private function getValueTransformer(): Transformer\Value
    {
        return $this->valueTransformer ??= new Transformer\Value($this->getSharedStrings(), $this->getStyles());
    }

    private function getWorksheetPaths(): array
    {
        return $this->worksheetPaths ??= new Worksheet($this->archive->extract('xl/workbook.xml'))->getWorksheetPaths($this->getRelationships());
    }
}
