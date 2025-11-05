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

namespace Valksor\XlsxParser\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Valksor\XlsxParser\Exception\InvalidArchiveException;
use Valksor\XlsxParser\Exception\InvalidIndexException;
use Valksor\XlsxParser\Exception\InvalidXLSXFileException;
use Valksor\XlsxParser\XLSXParser;

class XLSXParserTest extends TestCase
{
    public function testDataValidity(): void
    {
        $workbook = new XLSXParser()->open(__DIR__ . '/assets/mock.xlsx');
        $this->assertEquals(['data', ], $workbook->getWorksheets());
        $values = [];

        foreach ($workbook->getRows($workbook->getIndex('data')) as $key => $row) {
            $values[$key] = $row;
        }

        $this->assertCount(1001, $values);
        $data = [
            'JM1NC2MF8E0960570', 'Acura', 'TSX', 2012, 'Teal', 218514, 'Electric', 'Manual', 5.6, 272, 159, 1,
            73103.86, '5/10/2022', 'Augusto Grissett', 27, 'Male', '85426 International Trail', 'Néa Karyá',
            'Greece', '4/24/2018', '', 'XYZ789', 'Topicblab', 469689, '3/7/2029', 'Maecenas ut massa quis augue luctus tincidunt. Nulla mollis molestie lorem. Quisque ut erat.

Curabitur gravida nisi at nibh. In hac habitasse platea dictumst. Aliquam augue quam, sollicitudin vitae, consectetuer eget, rutrum at, lorem.

Integer tincidunt ante vel ipsum. Praesent blandit lacinia erat. Vestibulum sed magna at nunc commodo placerat.', true, 'Curabitur at ipsum ac tellus semper interdum. Mauris ullamcorper purus sit amet nulla. Quisque arcu libero, rutrum ac, lobortis vel, dapibus at, diam.', false,
        ];
        $this->assertEquals($data, $values[2]);
    }

    public function testOpen(): void
    {
        $workbook = new XLSXParser()->open(__DIR__ . '/assets/workbook.xlsx');
        $this->assertEquals(['worksheet', ], $workbook->getWorksheets());
        $values = [];

        foreach ($workbook->getRows($workbook->getIndex('worksheet')) as $key => $row) {
            $values[$key] = $row;
        }

        $this->assertCount(201, $values);
        $this->assertEquals('Alfred', $values[6][0]);
        $this->assertEquals(new DateTimeImmutable('2022-12-05'), $values[2][5]);
    }

    public function testOpenCellXfs(): void
    {
        $workbook = new XLSXParser()->open(__DIR__ . '/assets/samplefile.xlsx');
        $this->assertEquals(['Sample', 'Cities', ], $workbook->getWorksheets());
        $values = [];

        foreach ($workbook->getRows($workbook->getIndex('Cities')) as $row) {
            $values[] = $row;
        }

        foreach ($workbook->getRows($workbook->getIndex('Sample')) as $row) {
            $values[] = $row;
        }

        $this->assertCount(12, $values);
    }

    public function testOpenNotExists(): void
    {
        $this->expectException(InvalidArchiveException::class);
        $workbook = new XLSXParser()->open(__DIR__ . '/assets/workbook2.xlsx');
        $this->assertEquals(['worksheet', ], $workbook->getWorksheets());
    }

    public function testOpenNotXlsxXml(): void
    {
        $this->expectException(InvalidArchiveException::class);
        $workbook = new XLSXParser()->open(__DIR__ . '/assets/test.xml');
        $workbook->getIndex('worksheet');
    }

    public function testOpenNotXlsxZip(): void
    {
        $this->expectException(InvalidXLSXFileException::class);
        $workbook = new XLSXParser()->open(__DIR__ . '/assets/assets.zip');
        $workbook->getIndex('worksheet');
    }

    public function testOpenNotZip(): void
    {
        $this->expectException(InvalidArchiveException::class);
        $workbook = new XLSXParser()->open(__DIR__ . '/XLSXParserTest.php');
        $workbook->getIndex('worksheet');
    }

    public function testOpenWrongIndex(): void
    {
        $this->expectException(InvalidIndexException::class);
        $workbook = new XLSXParser()->open(__DIR__ . '/assets/workbook.xlsx');
        $workbook->getIndex('worksheet2');
    }
}
