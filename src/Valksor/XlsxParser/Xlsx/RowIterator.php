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
use Valksor\XlsxParser\Transformer\Column;
use Valksor\XlsxParser\Transformer\Value;
use XMLReader;

/**
 * @internal
 */
final class RowIterator implements Iterator
{
    private const string COLUMN = 'c';
    private const string ROW = 'row';
    private const string ROW_INDEX = 'r';
    private const string STYLE = 's';
    private const string TYPE = 't';
    private const string VALUE = 'v';
    private int $currentKey;
    private array $currentValue;
    private int $index;

    private ?Row $row = null;
    private string $style;
    private string $type;
    private bool $valid;
    private XMLReader $xml;
    private readonly Column $columnTransformer;

    public function __construct(
        private readonly Value $valueTransformer,
        private readonly string $path,
        ?Column $columnTransformer = null,
    ) {
        $this->columnTransformer = $columnTransformer ?? new Column();
    }

    public function current(): array
    {
        return $this->currentValue;
    }

    public function key(): int
    {
        return $this->currentKey;
    }

    public function next(): void
    {
        $this->valid = false;

        while ($this->xml->read()) {
            $this->processEndElement();

            if ($this->valid) {
                return;
            }

            $this->process();
        }
    }

    public function rewind(): void
    {
        $xml = new XMLReader();

        $this->xml = false === $xml->open($this->path) ? null : $xml;

        $this->next();
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    private function process(): void
    {
        if (XMLReader::ELEMENT === $this->xml->nodeType) {
            match ($this->xml->name) {
                self::ROW => $this->processRow(),
                self::COLUMN => $this->processColumn(),
                self::VALUE => $this->processValue(),
                default => $this->processDefault(),
            };
        }
    }

    private function processColumn(): void
    {
        $this->index = $this->columnTransformer->transform($this->xml->getAttribute(self::ROW_INDEX));
        $this->style = $this->xml->getAttribute(self::STYLE) ?? '';
        $this->type = $this->xml->getAttribute(self::TYPE) ?? '';
    }

    private function processDefault(): void
    {
        $this->row?->addValue($this->index, $this->xml->readString());
    }

    private function processEndElement(): void
    {
        if (XMLReader::END_ELEMENT === $this->xml->nodeType) {
            $this->processEndValue();
        }
    }

    private function processEndValue(): void
    {
        if (self::ROW === $this->xml->name) {
            $currentValue = $this->row?->getData();

            if ([] !== $currentValue) {
                $this->currentValue = $currentValue;
                $this->valid = true;
            }
        }
    }

    private function processRow(): void
    {
        $this->currentKey = (int) $this->xml->getAttribute(self::ROW_INDEX);
        $this->row = new Row();
    }

    private function processValue(): void
    {
        $this->row?->addValue($this->index, $this->valueTransformer->transform($this->xml->readString(), $this->type, $this->style));
    }
}
