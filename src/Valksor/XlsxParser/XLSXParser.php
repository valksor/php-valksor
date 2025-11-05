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

namespace Valksor\XlsxParser;

use Valksor\XlsxParser\Contracts\XLSXInterface;
use Valksor\XlsxParser\Contracts\XLSXParserInterface;
use Valksor\XlsxParser\Xlsx\Archive;
use Valksor\XlsxParser\Xlsx\XLSX;

final class XLSXParser implements XLSXParserInterface
{
    public function open(
        string $path,
    ): XLSXInterface {
        return new XLSX(
            new Archive($path),
        );
    }
}
