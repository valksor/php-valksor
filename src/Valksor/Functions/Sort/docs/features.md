# Valksor Functions: Sort - Features

This document lists all the functions available in the Valksor Functions: Sort package.

## Sorting Algorithms

### bubbleSort()

```php
public function bubbleSort(
    array &$array,
): void
```

Sorts an array in place using the bubble sort algorithm.

Parameters:
- `$array`: The array to sort (passed by reference)

### mergeSort()

```php
public function mergeSort(
    array $array,
): array
```

Sorts an array using the merge sort algorithm.

Parameters:
- `$array`: The array to sort

Returns a new sorted array.

### merge()

```php
public function merge(
    array $left,
    array $right,
): array
```

Merges two sorted arrays into a single sorted array.

Parameters:
- `$left`: The first sorted array
- `$right`: The second sorted array

Returns a new sorted array containing all elements from both input arrays.

## Specialized Sorting

### sortByParameter()

```php
public function sortByParameter(
    array|object $data,
    string $parameter,
    string $order = 'ASC',
): object|array
```

Sorts an array or object by a specific parameter.

Parameters:
- `$data`: The array or object to sort
- `$parameter`: The parameter to sort by
- `$order`: The sort order ('ASC' or 'DESC')

Returns the sorted array or object.

Throws an InvalidArgumentException if the parameter doesn't exist in the sortable variable.

### stableSort()

```php
public function stableSort(
    array $elements,
    callable $getComparedValue,
    callable $compareValues,
): array
```

Performs a stable sort on an array of elements.

Parameters:
- `$elements`: The array to sort
- `$getComparedValue`: A callable that extracts the value to compare from an element
- `$compareValues`: A callable that compares two values and returns an integer (-1, 0, or 1)

Returns a new sorted array.

### usort()

```php
public function usort(
    string $parameter,
    string $order,
): Closure
```

Creates a closure for use with PHP's usort function to sort arrays or objects by a specific parameter.

Parameters:
- `$parameter`: The parameter to sort by
- `$order`: The sort order ('ASC' or 'DESC')

Returns a closure that can be used with usort.
