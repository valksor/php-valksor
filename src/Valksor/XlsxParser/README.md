<h1 align="center">XLSX Parser</h1>

<p align="center">Very simple to implement xlsx parser to extract data from spreadsheets for php 8.1+ </p>

What is it?
---
**XLSXParser** is blazingly fast xlsx parser for **php 8.1+**. It is made as a simple tool to get job done. No fancy options of any kind
and no need for any extra libraries other than need for `zip` and `xmlreader` php extensions.

---
* Initialize class.
* Open workbook.
* Choose worksheet.
* And iterate through receiving each row as an array.

---
Installation
---
The recommended way to install is via Composer:

```shell
composer require valksor/php-xlsx-parser
```
Usage
---

```php
use Valksor\XlsxParser\XLSXParser;

$workbook = (new XLSXParser())->open('workbook.xlsx');

foreach ($workbook->getRows($workbook->getIndex('worksheet')) as $key => $values) {
    var_dump($key, $values);
}
```
