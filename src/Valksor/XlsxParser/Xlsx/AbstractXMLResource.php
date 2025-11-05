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

use Throwable;
use Valksor\XlsxParser\Exception\InvalidXLSXFileException;
use XMLReader;

/**
 * @internal
 */
abstract class AbstractXMLResource
{
    private ?XMLReader $xml = null;

    public function __construct(
        private readonly string $path,
    ) {
    }

    public function __destruct()
    {
        $this->closeXMLReader();
    }

    protected function closeXMLReader(): void
    {
        $this->xml?->close();
        $this->xml = null;
    }

    protected function createXMLReader(): XMLReader
    {
        return $this->validateXMLReader(new XMLReader());
    }

    protected function getXMLReader(): XMLReader
    {
        return $this->xml ??= $this->createXMLReader();
    }

    private function validateXMLReader(
        XMLReader $xml,
    ): XMLReader {
        try {
            @$xml->open($this->path);
            $xml->read();
        } catch (Throwable $throwable) {
            throw new InvalidXLSXFileException($this->path, $throwable);
        }

        return $xml;
    }
}
