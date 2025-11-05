# Valksor XlsxParser

A blazingly fast and simple XLSX parser for PHP 8.4+ that extracts data from Excel spreadsheets without any external dependencies.

## Features

- **Blazing Fast Performance**: Optimized for speed with minimal memory usage
- **Simple API**: Easy-to-use interface with just 4 steps to parse data
- **Zero External Dependencies**: Only requires PHP's built-in `zip` and `xmlreader` extensions
- **Memory Efficient**: Streams data without loading entire spreadsheet into memory
- **PHP 8.4+ Support**: Built for modern PHP with strict typing
- **Multiple Worksheets**: Support for parsing different worksheets in the same workbook

**XLSXParser** is designed as a simple tool to get the job done. No fancy options of any kind and no need for any extra libraries other than the required `zip` and `xmlreader` PHP extensions.

The parsing workflow is straightforward:
1. Initialize class
2. Open workbook
3. Choose worksheet
4. Iterate through rows receiving each row as an array

## Installation

The recommended way to install is via Composer:

```bash
composer require valksor/php-xlsx-parser
```

## Requirements

- **PHP**: 8.4 or higher
- **Extensions**:
  - `zip` - For extracting XLSX files
  - `xmlreader` - For parsing XML data efficiently
## Usage

### Basic Usage

```php
use Valksor\XlsxParser\XLSXParser;

// Create parser instance and open workbook
$workbook = new XLSXParser()->open('workbook.xlsx');

// Iterate through rows of the first worksheet
foreach ($workbook->getRows($workbook->getIndex('worksheet')) as $key => $values) {
    echo "Row {$key}: " . implode(', ', $values) . "\n";
}
```

### Working with Multiple Worksheets

```php
use Valksor\XlsxParser\XLSXParser;

$workbook = new XLSXParser()->open('workbook.xlsx');

// Get all available worksheets
$worksheets = $workbook->getWorksheets();
foreach ($worksheets as $worksheet) {
    echo "Processing worksheet: {$worksheet}\n";

    foreach ($workbook->getRows($workbook->getIndex($worksheet)) as $rowIndex => $rowData) {
        // Process each row
        processRow($rowData);
    }
}
```

### Processing Data

```php
use Valksor\XlsxParser\XLSXParser;

$workbook = new XLSXParser()->open('data.xlsx');
$rows = $workbook->getRows($workbook->getIndex('Sheet1'));

$headers = null;
foreach ($rows as $rowIndex => $rowData) {
    if ($rowIndex === 0) {
        // First row contains headers
        $headers = $rowData;
        continue;
    }

    // Combine headers with data
    $dataRow = array_combine($headers, $rowData);

    // Process your data
    echo "Name: {$dataRow['Name']}, Email: {$dataRow['Email']}\n";
}
```

### Error Handling

```php
use Valksor\XlsxParser\XLSXParser;

try {
    $workbook = new XLSXParser()->open('spreadsheet.xlsx');

    if (!$workbook->hasWorksheet('Data')) {
        throw new Exception('Worksheet "Data" not found');
    }

    $rows = $workbook->getRows($workbook->getIndex('Data'));

    foreach ($rows as $row) {
        // Process row data
    }

} catch (Exception $e) {
    echo "Error parsing XLSX file: " . $e->getMessage() . "\n";
}
```

## Contributing

Contributions are welcome! To ensure consistency across the project, please follow these guidelines:

- **Code Style**: Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- **Testing**: Include tests for new features and bug fixes
- **Documentation**: Update documentation as needed
- **Pull Requests**: Provide clear descriptions of changes and their purpose

## Security

If you discover any security-related issues, please email us at packages@valksor.com instead of using the issue tracker.

## Support

- **Documentation**: [Valksor Documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues)
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions)

## Credits

- **Author**: [Valksor Team](https://github.com/valksor)
- **Contributors**: All [contributors](https://github.com/valksor/php-valksor/graphs/contributors) who have helped improve this package
- **Inspiration**: Built for the modern PHP ecosystem with simplicity and performance in mind

## License

This package is licensed under the [BSD-3-Clause License](https://github.com/valksor/php-plugin/blob/master/LICENSE).

## About Valksor

Valksor is a collection of high-quality PHP libraries designed to make development faster, easier, and more enjoyable. Our libraries are built with modern PHP best practices and focus on performance, security, and developer experience.

Explore our other packages:
- [Valksor Functions](https://github.com/valksor/php-functions) - Utility functions for common tasks
- [Valksor Component SSE](https://github.com/valksor/php-component-sse) - Server-Sent Events implementation
- [Valksor Bundle](https://github.com/valksor/php-bundle) - Symfony bundle integration

Visit the [Valksor GitHub Organization](https://github.com/valksor) to learn more about our ecosystem.
