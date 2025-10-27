# Valksor Functions: Pagination - Features

This document lists all the functions available in the Valksor Functions: Pagination package.

## Pagination Functions

### paginate()

```php
public function paginate(
    int $visible,
    int $total,
    int $current,
    int $indicator = -1,
): array
```

Generates pagination data based on the specified parameters.

Parameters:
- `$visible`: Number of page links to display (minimum 5)
- `$total`: Total number of pages
- `$current`: Current page number
- `$indicator`: Value to use for indicating omitted pages (default: -1)

Returns:
- An array of page numbers with indicators for omitted pages

#### Validation

The function performs several validations:
- The number of visible pages must be at least 5
- The total number of pages must be at least 1
- The current page must be between 1 and the total number of pages
- The indicator value must not be a valid page number (between 1 and total)

#### Pagination Scenarios

The pagination algorithm intelligently handles different scenarios:

1. **All Pages Visible**: When the total number of pages is less than or equal to the number of visible pages
   ```php
   $pages = $pagination->paginate(10, 8, 4); // [1, 2, 3, 4, 5, 6, 7, 8]
   ```

2. **Single Omitted Section**: When the current page is near the beginning or end
   ```php
   // Current page near beginning
   $pages = $pagination->paginate(7, 20, 3); // [1, 2, 3, 4, 5, -1, 20]

   // Current page near end
   $pages = $pagination->paginate(7, 20, 18); // [1, -1, 16, 17, 18, 19, 20]
   ```

3. **Two Omitted Sections**: When the current page is in the middle of a large set
   ```php
   $pages = $pagination->paginate(7, 20, 10); // [1, -1, 8, 9, 10, 11, 12, -1, 20]
   ```

### Example

```php
use Valksor\Functions\Pagination;

// Basic usage
$visible = 7;      // Number of page links to display
$total = 20;       // Total number of pages
$current = 10;     // Current page
$indicator = -1;   // Value to indicate omitted pages

$pages = Pagination::paginate($visible, $total, $current, $indicator);
echo "Pagination for page {$current} of {$total} (showing {$visible} links): " . implode(', ', $pages) . "\n";
// Output: Pagination for page 10 of 20 (showing 7 links): 1, -1, 8, 9, 10, 11, 12, -1, 20

// Different scenarios based on current page position
$scenarios = [
    1,    // First page
    2,    // Near beginning
    5,    // Middle-beginning
    10,   // Middle
    15,   // Middle-end
    19,   // Near end
    20    // Last page
];

echo "\nPagination scenarios with {$visible} visible links and {$total} total pages:\n";
foreach ($scenarios as $currentPage) {
    $pages = Pagination::paginate($visible, $total, $currentPage);
    echo "Page {$currentPage}: " . implode(', ', $pages) . "\n";
}
// Output:
// Page 1: 1, 2, 3, 4, 5, -1, 20
// Page 2: 1, 2, 3, 4, 5, -1, 20
// Page 5: 1, 2, 3, 4, 5, 6, 7, -1, 20
// Page 10: 1, -1, 8, 9, 10, 11, 12, -1, 20
// Page 15: 1, -1, 13, 14, 15, 16, 17, -1, 20
// Page 19: 1, -1, 16, 17, 18, 19, 20
// Page 20: 1, -1, 16, 17, 18, 19, 20

// Changing the number of visible links
$visibleOptions = [5, 7, 9, 11];
$currentPage = 10;

echo "\nImpact of changing visible links (current page: {$currentPage}):\n";
foreach ($visibleOptions as $visible) {
    $pages = Pagination::paginate($visible, $total, $currentPage);
    echo "Visible {$visible}: " . implode(', ', $pages) . "\n";
}
// Output:
// Visible 5: 1, -1, 9, 10, 11, -1, 20
// Visible 7: 1, -1, 8, 9, 10, 11, 12, -1, 20
// Visible 9: 1, -1, 6, 7, 8, 9, 10, 11, 12, 13, 14, -1, 20
// Visible 11: 1, -1, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, -1, 20

// Edge cases
try {
    // Minimum visible pages (should be at least 5)
    $pages = Pagination::paginate(3, 10, 5);
} catch (\Exception $e) {
    echo "\nError with too few visible pages: " . $e->getMessage() . "\n";
}

try {
    // Current page out of range
    $pages = Pagination::paginate(7, 10, 15);
} catch (\Exception $e) {
    echo "Error with current page out of range: " . $e->getMessage() . "\n";
}

try {
    // Invalid indicator (must not be a valid page number)
    $pages = Pagination::paginate(7, 10, 5, 5);
} catch (\Exception $e) {
    echo "Error with invalid indicator: " . $e->getMessage() . "\n";
}

// Small total pages (all pages visible)
$smallTotal = 6;
$pages = Pagination::paginate(7, $smallTotal, 3);
echo "\nSmall total ({$smallTotal} pages): " . implode(', ', $pages) . "\n";
// Output: Small total (6 pages): 1, 2, 3, 4, 5, 6

// Custom indicator
$customIndicator = 0;
$pages = Pagination::paginate(7, 20, 10, $customIndicator);
echo "Custom indicator ({$customIndicator}): " . implode(', ', $pages) . "\n";
// Output: Custom indicator (0): 1, 0, 8, 9, 10, 11, 12, 0, 20

// Practical use case: Implementing pagination in a web application
function renderPagination($currentPage, $totalItems, $itemsPerPage, $visiblePages = 7) {
    // Calculate total pages
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Get pagination array
    $pages = Pagination::paginate($visiblePages, $totalPages, $currentPage);

    // Build HTML
    $html = '<nav aria-label="Page navigation"><ul class="pagination">';

    // Previous button
    $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
    $prevPage = max(1, $currentPage - 1);
    $html .= '<li class="page-item' . $prevDisabled . '">
                <a class="page-link" href="?page=' . $prevPage . '" aria-label="Previous">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>';

    // Page numbers
    $indicator = -1; // The value used for omitted pages
    foreach ($pages as $page) {
        if ($page === $indicator) {
            // Render ellipsis for omitted pages
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        } else {
            // Render regular page link
            $active = $page === $currentPage ? ' active' : '';
            $html .= '<li class="page-item' . $active . '">
                        <a class="page-link" href="?page=' . $page . '">' . $page . '</a>
                      </li>';
        }
    }

    // Next button
    $nextDisabled = $currentPage >= $totalPages ? ' disabled' : '';
    $nextPage = min($totalPages, $currentPage + 1);
    $html .= '<li class="page-item' . $nextDisabled . '">
                <a class="page-link" href="?page=' . $nextPage . '" aria-label="Next">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>';

    $html .= '</ul></nav>';

    return $html;
}

// Example usage of the renderPagination function
$currentPage = 7;
$totalItems = 235;
$itemsPerPage = 12;

$paginationHtml = renderPagination($currentPage, $totalItems, $itemsPerPage);
echo "\nHTML Pagination for page {$currentPage} (total items: {$totalItems}, per page: {$itemsPerPage}):\n";
echo $paginationHtml . "\n";

// Function to get paginated data from an array
function getPaginatedData($data, $currentPage, $itemsPerPage) {
    $totalItems = count($data);
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Validate current page
    if ($currentPage < 1) {
        $currentPage = 1;
    } elseif ($currentPage > $totalPages) {
        $currentPage = $totalPages;
    }

    // Calculate offset
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Get items for current page
    $items = array_slice($data, $offset, $itemsPerPage);

    return [
        'items' => $items,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'itemsPerPage' => $itemsPerPage,
        'pagination' => Pagination::paginate(7, $totalPages, $currentPage)
    ];
}

// Example data (list of products)
$products = [];
for ($i = 1; $i <= 50; $i++) {
    $products[] = [
        'id' => $i,
        'name' => 'Product ' . $i,
        'price' => rand(10, 100) / 10
    ];
}

// Get paginated products
$page = 3; // Current page
$perPage = 10; // Items per page
$result = getPaginatedData($products, $page, $perPage);

echo "\nPaginated Products (Page {$result['currentPage']} of {$result['totalPages']}):\n";
echo "Showing items " . (($result['currentPage'] - 1) * $result['itemsPerPage'] + 1) .
     " to " . min($result['currentPage'] * $result['itemsPerPage'], $result['totalItems']) .
     " of {$result['totalItems']} total\n";

echo "Pagination: " . implode(', ', $result['pagination']) . "\n";

echo "Items on this page:\n";
foreach ($result['items'] as $product) {
    echo "- {$product['name']} (\${$product['price']})\n";
}
```
