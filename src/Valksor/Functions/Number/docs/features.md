# Valksor Functions: Number - Features

This document lists all the functions available in the Valksor Functions: Number package.

## Prime Number Functions

### isPrime()

```php
public function isPrime(
    int $number,
    bool $override = false,
): bool
```

Checks if a number is prime using multiple algorithms.

Parameters:

- `$number`: The number to check
- `$override`: Whether to override the default behavior

Returns a boolean indicating whether the number is prime.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - checking if a number is prime
$isPrime17 = Number::isPrime(17);
echo "Is 17 prime? " . ($isPrime17 ? 'Yes' : 'No') . "\n";
// Output: Is 17 prime? Yes

$isPrime20 = Number::isPrime(20);
echo "Is 20 prime? " . ($isPrime20 ? 'Yes' : 'No') . "\n";
// Output: Is 20 prime? No

// Checking small prime numbers
$smallPrimes = [2, 3, 5, 7, 11, 13];
foreach ($smallPrimes as $number) {
    echo "{$number} is " . (Number::isPrime($number) ? 'prime' : 'not prime') . "\n";
}
// Output:
// 2 is prime
// 3 is prime
// 5 is prime
// 7 is prime
// 11 is prime
// 13 is prime

// Checking non-prime numbers
$nonPrimes = [1, 4, 6, 8, 9, 10, 12];
foreach ($nonPrimes as $number) {
    echo "{$number} is " . (Number::isPrime($number) ? 'prime' : 'not prime') . "\n";
}
// Output:
// 1 is not prime
// 4 is not prime
// 6 is not prime
// 8 is not prime
// 9 is not prime
// 10 is not prime
// 12 is not prime

// Checking larger prime numbers
$largerPrimes = [97, 101, 997, 1009];
foreach ($largerPrimes as $number) {
    echo "{$number} is " . (Number::isPrime($number) ? 'prime' : 'not prime') . "\n";
}
// Output:
// 97 is prime
// 101 is prime
// 997 is prime
// 1009 is prime

// Using the override parameter
// The override parameter can be used to force a specific algorithm
// By default, the function uses optimized methods for different ranges
$isPrime997WithOverride = Number::isPrime(997, true);
echo "Is 997 prime (with override)? " . ($isPrime997WithOverride ? 'Yes' : 'No') . "\n";
// Output: Is 997 prime (with override)? Yes

// Practical use case: Finding all prime numbers in a range
function findPrimesInRange($start, $end) {
    $primes = [];
    for ($i = $start; $i <= $end; $i++) {
        if (Number::isPrime($i)) {
            $primes[] = $i;
        }
    }
    return $primes;
}

$primesFrom50To100 = findPrimesInRange(50, 100);
echo "Prime numbers between 50 and 100: " . implode(', ', $primesFrom50To100) . "\n";
// Output: Prime numbers between 50 and 100: 53, 59, 61, 67, 71, 73, 79, 83, 89, 97

// Practical use case: Checking if a number is a prime factor of another number
function isPrimeFactor($factor, $number) {
    if (!Number::isPrime($factor)) {
        return false; // Not a prime number
    }

    return $number % $factor === 0; // Check if it's a factor
}

$number = 42;
$potentialFactors = [2, 3, 5, 7, 11];
echo "Prime factors of {$number}: ";
$primeFactors = [];
foreach ($potentialFactors as $factor) {
    if (isPrimeFactor($factor, $number)) {
        $primeFactors[] = $factor;
    }
}
echo implode(', ', $primeFactors) . "\n";
// Output: Prime factors of 42: 2, 3, 7
```

### isPrimal()

```php
public function isPrimal(
    int $number,
): bool
```

Internal helper function for prime number checking.

Parameters:

- `$number`: The number to check

Returns a boolean indicating whether the number is prime.

Example:

```php
use Valksor\Functions\Number;

// Note: This is an internal helper function, and in most cases,
// you should use isPrime() instead. This example is for demonstration purposes.

// Basic usage
$isPrimal23 = Number::isPrimal(23);
echo "Is 23 primal? " . ($isPrimal23 ? 'Yes' : 'No') . "\n";
// Output: Is 23 primal? Yes

$isPrimal15 = Number::isPrimal(15);
echo "Is 15 primal? " . ($isPrimal15 ? 'Yes' : 'No') . "\n";
// Output: Is 15 primal? No

// This function uses a basic algorithm to check primality
// It checks if the number is divisible by any integer from 2 to sqrt(number)
function customIsPrimal($number) {
    if ($number < 2) {
        return false;
    }

    $sqrt = sqrt($number);
    for ($i = 2; $i <= $sqrt; $i++) {
        if ($number % $i === 0) {
            return false;
        }
    }

    return true;
}

// Compare with our custom implementation
$testNumbers = [2, 3, 7, 11, 13, 15, 17, 19, 21, 23];
echo "Comparing isPrimal() with custom implementation:\n";
foreach ($testNumbers as $number) {
    $valksor = Number::isPrimal($number);
    $custom = customIsPrimal($number);
    echo "{$number}: Valksor: " . ($valksor ? 'Prime' : 'Not prime') .
         ", Custom: " . ($custom ? 'Prime' : 'Not prime') .
         ", Match: " . ($valksor === $custom ? 'Yes' : 'No') . "\n";
}
// Output should show matching results for all numbers

// The difference between isPrime() and isPrimal()
// isPrime() uses different algorithms based on the number's size
// while isPrimal() uses a single algorithm
$largeNumber = 997;
$isPrime = Number::isPrime($largeNumber);
$isPrimal = Number::isPrimal($largeNumber);
echo "For {$largeNumber}: isPrime(): " . ($isPrime ? 'Prime' : 'Not prime') .
     ", isPrimal(): " . ($isPrimal ? 'Prime' : 'Not prime') . "\n";
// Both should return that 997 is prime, but isPrime() might be more efficient
```

### isPrimeBelow1000()

```php
public function isPrimeBelow1000(
    int $number,
): bool
```

Optimized function for checking if a number below 1000 is prime.

Parameters:

- `$number`: The number to check (should be below 1000)

Returns a boolean indicating whether the number is prime.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - checking small prime numbers
$smallPrimes = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29];
foreach ($smallPrimes as $number) {
    $isPrime = Number::isPrimeBelow1000($number);
    echo "{$number} is " . ($isPrime ? 'prime' : 'not prime') . "\n";
}
// Output:
// 2 is prime
// 3 is prime
// 5 is prime
// 7 is prime
// 11 is prime
// 13 is prime
// 17 is prime
// 19 is prime
// 23 is prime
// 29 is prime

// Checking non-prime numbers below 1000
$nonPrimes = [1, 4, 6, 8, 9, 10, 12, 15, 100, 999];
foreach ($nonPrimes as $number) {
    $isPrime = Number::isPrimeBelow1000($number);
    echo "{$number} is " . ($isPrime ? 'prime' : 'not prime') . "\n";
}
// Output:
// 1 is not prime
// 4 is not prime
// 6 is not prime
// 8 is not prime
// 9 is not prime
// 10 is not prime
// 12 is not prime
// 15 is not prime
// 100 is not prime
// 999 is not prime

// Checking prime numbers close to 1000
$largerPrimes = [997, 991, 983, 977, 971];
foreach ($largerPrimes as $number) {
    $isPrime = Number::isPrimeBelow1000($number);
    echo "{$number} is " . ($isPrime ? 'prime' : 'not prime') . "\n";
}
// Output:
// 997 is prime
// 991 is prime
// 983 is prime
// 977 is prime
// 971 is prime

// Note: This function is optimized for numbers below 1000
// For numbers >= 1000, the behavior might be undefined or incorrect
// It's recommended to use isPrime() for numbers >= 1000
try {
    $result = Number::isPrimeBelow1000(1000);
    echo "1000 is " . ($result ? 'prime' : 'not prime') . " (but this result might be unreliable)\n";

    $result = Number::isPrimeBelow1000(1009);
    echo "1009 is " . ($result ? 'prime' : 'not prime') . " (but this result might be unreliable)\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Comparing performance with isPrime() for small numbers
// In a real application, you would use microtime() to measure performance
echo "\nComparing isPrimeBelow1000() with isPrime() for small numbers:\n";

$testNumbers = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47];
foreach ($testNumbers as $number) {
    $result1 = Number::isPrimeBelow1000($number);
    $result2 = Number::isPrime($number);

    echo "{$number}: isPrimeBelow1000(): " . ($result1 ? 'Prime' : 'Not prime') .
         ", isPrime(): " . ($result2 ? 'Prime' : 'Not prime') .
         ", Match: " . ($result1 === $result2 ? 'Yes' : 'No') . "\n";
}
// Both functions should return the same results for numbers below 1000

// Practical use case: Finding all prime numbers below 100
function findPrimesBelow100() {
    $primes = [];
    for ($i = 2; $i < 100; $i++) {
        if (Number::isPrimeBelow1000($i)) {
            $primes[] = $i;
        }
    }
    return $primes;
}

$primesBelow100 = findPrimesBelow100();
echo "\nPrime numbers below 100: " . implode(', ', $primesBelow100) . "\n";
// Output: Prime numbers below 100: 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97
```

### isPrimeGmp()

```php
public function isPrimeGmp(
    int $number,
): bool
```

Uses the GMP extension to check if a number is prime (more efficient for large numbers).

Parameters:

- `$number`: The number to check

Returns a boolean indicating whether the number is prime.

Example:

```php
use Valksor\Functions\Number;

// Note: This function requires the GMP extension to be installed
// If GMP is not available, it will throw an exception or fall back to another method

// Basic usage - checking if a number is prime using GMP
try {
    $isPrime17 = Number::isPrimeGmp(17);
    echo "Is 17 prime? " . ($isPrime17 ? 'Yes' : 'No') . "\n";
    // Output: Is 17 prime? Yes

    $isPrime20 = Number::isPrimeGmp(20);
    echo "Is 20 prime? " . ($isPrime20 ? 'Yes' : 'No') . "\n";
    // Output: Is 20 prime? No
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "GMP extension might not be available on your system.\n";
}

// Checking large prime numbers
// GMP is especially useful for large numbers
$largePrimes = [10007, 100003, 1000003];
try {
    foreach ($largePrimes as $number) {
        $isPrime = Number::isPrimeGmp($number);
        echo "{$number} is " . ($isPrime ? 'prime' : 'not prime') . "\n";
    }
    // Output:
    // 10007 is prime
    // 100003 is prime
    // 1000003 is prime
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Checking Mersenne primes (2^n - 1)
// These are prime numbers of the form 2^n - 1
$mersenneExponents = [2, 3, 5, 7, 13, 17, 19];
try {
    foreach ($mersenneExponents as $exponent) {
        $mersenne = (2 ** $exponent) - 1;
        $isPrime = Number::isPrimeGmp($mersenne);
        echo "2^{$exponent} - 1 = {$mersenne} is " . ($isPrime ? 'prime' : 'not prime') . "\n";
    }
    // Output:
    // 2^2 - 1 = 3 is prime
    // 2^3 - 1 = 7 is prime
    // 2^5 - 1 = 31 is prime
    // 2^7 - 1 = 127 is prime
    // 2^13 - 1 = 8191 is prime
    // 2^17 - 1 = 131071 is prime
    // 2^19 - 1 = 524287 is prime
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Comparing with isPrime() for large numbers
// In a real application, you would use microtime() to measure performance
echo "\nComparing isPrimeGmp() with isPrime() for large numbers:\n";
$testNumbers = [9973, 9967, 9949, 9941, 9931];
try {
    foreach ($testNumbers as $number) {
        // Start time measurement for isPrimeGmp()
        $startGmp = microtime(true);
        $resultGmp = Number::isPrimeGmp($number);
        $endGmp = microtime(true);
        $timeGmp = ($endGmp - $startGmp) * 1000; // Convert to milliseconds

        // Start time measurement for isPrime()
        $startRegular = microtime(true);
        $resultRegular = Number::isPrime($number);
        $endRegular = microtime(true);
        $timeRegular = ($endRegular - $startRegular) * 1000; // Convert to milliseconds

        echo "{$number}: isPrimeGmp(): " . ($resultGmp ? 'Prime' : 'Not prime') .
             " ({$timeGmp} ms), isPrime(): " . ($resultRegular ? 'Prime' : 'Not prime') .
             " ({$timeRegular} ms), Match: " . ($resultGmp === $resultRegular ? 'Yes' : 'No') . "\n";
    }
    // Both functions should return the same results, but isPrimeGmp() might be faster for large numbers
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Practical use case: Checking if a large number is a safe prime
// A safe prime is a prime number of the form 2p + 1, where p is also prime
function isSafePrime($number) {
    try {
        // First, check if the number itself is prime
        if (!Number::isPrimeGmp($number)) {
            return false;
        }

        // Then check if (number-1)/2 is also prime
        $p = ($number - 1) / 2;
        return Number::isPrimeGmp($p);
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

// Check some known safe primes
$safePrimes = [5, 7, 11, 23, 47, 59, 83, 107, 167, 179];
foreach ($safePrimes as $number) {
    echo "{$number} is " . (isSafePrime($number) ? 'a safe prime' : 'not a safe prime') . "\n";
}
// Output should confirm these are all safe primes
```

## Distance Calculation

### distanceBetweenPoints()

```php
public function distanceBetweenPoints(
    float $latitude1,
    float $longitude1,
    float $latitude2,
    float $longitude2,
    bool $km = true,
    int $precision = 4,
): float
```

Calculates the distance between two geographical points.

Parameters:

- `$latitude1`: Latitude of the first point
- `$longitude1`: Longitude of the first point
- `$latitude2`: Latitude of the second point
- `$longitude2`: Longitude of the second point
- `$km`: Whether to return the distance in kilometers (true) or miles (false)
- `$precision`: The number of decimal places in the result

Returns the distance between the two points.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - calculate distance between two points in kilometers (default)
// New York City coordinates
$nyLat = 40.7128;
$nyLon = -74.0060;

// Los Angeles coordinates
$laLat = 34.0522;
$laLon = -118.2437;

$distanceKm = Number::distanceBetweenPoints($nyLat, $nyLon, $laLat, $laLon);
echo "Distance from New York to Los Angeles: {$distanceKm} km\n";
// Output: Distance from New York to Los Angeles: 3935.9453 km

// Calculate distance in miles
$distanceMiles = Number::distanceBetweenPoints($nyLat, $nyLon, $laLat, $laLon, false);
echo "Distance from New York to Los Angeles: {$distanceMiles} miles\n";
// Output: Distance from New York to Los Angeles: 2445.5953 miles

// Using different precision
$distanceKmLowPrecision = Number::distanceBetweenPoints($nyLat, $nyLon, $laLat, $laLon, true, 1);
echo "Distance (1 decimal place): {$distanceKmLowPrecision} km\n";
// Output: Distance (1 decimal place): 3935.9 km

$distanceKmHighPrecision = Number::distanceBetweenPoints($nyLat, $nyLon, $laLat, $laLon, true, 6);
echo "Distance (6 decimal places): {$distanceKmHighPrecision} km\n";
// Output: Distance (6 decimal places): 3935.945312 km

// Calculate distances between multiple cities
$cities = [
    'New York' => ['lat' => 40.7128, 'lon' => -74.0060],
    'Los Angeles' => ['lat' => 34.0522, 'lon' => -118.2437],
    'Chicago' => ['lat' => 41.8781, 'lon' => -87.6298],
    'Houston' => ['lat' => 29.7604, 'lon' => -95.3698],
    'Miami' => ['lat' => 25.7617, 'lon' => -80.1918]
];

echo "\nDistances between cities:\n";
foreach ($cities as $city1Name => $city1) {
    foreach ($cities as $city2Name => $city2) {
        if ($city1Name !== $city2Name) {
            $distance = Number::distanceBetweenPoints(
                $city1['lat'], $city1['lon'],
                $city2['lat'], $city2['lon'],
                true, // kilometers
                1 // 1 decimal place
            );
            echo "{$city1Name} to {$city2Name}: {$distance} km\n";
        }
    }
}

// Practical use case: Finding nearby locations
function findNearbyLocations($userLat, $userLon, $locations, $maxDistance) {
    $nearbyLocations = [];

    foreach ($locations as $name => $location) {
        $distance = Number::distanceBetweenPoints(
            $userLat, $userLon,
            $location['lat'], $location['lon']
        );

        if ($distance <= $maxDistance) {
            $nearbyLocations[$name] = [
                'lat' => $location['lat'],
                'lon' => $location['lon'],
                'distance' => $distance
            ];
        }
    }

    // Sort by distance
    uasort($nearbyLocations, function($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    return $nearbyLocations;
}

// Example: User in Chicago looking for cities within 1500 km
$userLat = 41.8781; // Chicago latitude
$userLon = -87.6298; // Chicago longitude
$maxDistance = 1500; // km

$nearbyLocations = findNearbyLocations($userLat, $userLon, $cities, $maxDistance);

echo "\nCities within {$maxDistance} km of Chicago:\n";
foreach ($nearbyLocations as $name => $location) {
    echo "{$name}: {$location['distance']} km\n";
}

// Practical use case: Calculating the total distance of a route
function calculateRouteDistance($waypoints) {
    $totalDistance = 0;
    $count = count($waypoints);

    for ($i = 0; $i < $count - 1; $i++) {
        $point1 = $waypoints[$i];
        $point2 = $waypoints[$i + 1];

        $distance = Number::distanceBetweenPoints(
            $point1['lat'], $point1['lon'],
            $point2['lat'], $point2['lon']
        );

        $totalDistance += $distance;
    }

    return $totalDistance;
}

// Example: Road trip from New York to Miami with stops
$roadTrip = [
    ['name' => 'New York', 'lat' => 40.7128, 'lon' => -74.0060],
    ['name' => 'Philadelphia', 'lat' => 39.9526, 'lon' => -75.1652],
    ['name' => 'Washington DC', 'lat' => 38.9072, 'lon' => -77.0369],
    ['name' => 'Charlotte', 'lat' => 35.2271, 'lon' => -80.8431],
    ['name' => 'Atlanta', 'lat' => 33.7490, 'lon' => -84.3880],
    ['name' => 'Orlando', 'lat' => 28.5383, 'lon' => -81.3792],
    ['name' => 'Miami', 'lat' => 25.7617, 'lon' => -80.1918]
];

$totalDistance = calculateRouteDistance($roadTrip);
echo "\nTotal road trip distance from New York to Miami: " . round($totalDistance, 2) . " km\n";
```

### distanceInKm()

```php
public function distanceInKm(
    float $latitude1,
    float $longitude1,
    float $latitude2,
    float $longitude2,
    int $precision = 4,
): float
```

Calculates the distance between two geographical points in kilometers.

Parameters:

- `$latitude1`: Latitude of the first point
- `$longitude1`: Longitude of the first point
- `$latitude2`: Latitude of the second point
- `$longitude2`: Longitude of the second point
- `$precision`: The number of decimal places in the result

Returns the distance in kilometers.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - calculate distance between two points in kilometers
// Paris coordinates
$parisLat = 48.8566;
$parisLon = 2.3522;

// Berlin coordinates
$berlinLat = 52.5200;
$berlinLon = 13.4050;

$distance = Number::distanceInKm($parisLat, $parisLon, $berlinLat, $berlinLon);
echo "Distance from Paris to Berlin: {$distance} km\n";
// Output: Distance from Paris to Berlin: 878.4199 km

// Using different precision
$distanceLowPrecision = Number::distanceInKm($parisLat, $parisLon, $berlinLat, $berlinLon, 1);
echo "Distance (1 decimal place): {$distanceLowPrecision} km\n";
// Output: Distance (1 decimal place): 878.4 km

$distanceHighPrecision = Number::distanceInKm($parisLat, $parisLon, $berlinLat, $berlinLon, 6);
echo "Distance (6 decimal places): {$distanceHighPrecision} km\n";
// Output: Distance (6 decimal places): 878.419922 km

// Comparing with distanceBetweenPoints() function
$distanceMethod1 = Number::distanceInKm($parisLat, $parisLon, $berlinLat, $berlinLon);
$distanceMethod2 = Number::distanceBetweenPoints($parisLat, $parisLon, $berlinLat, $berlinLon, true);
echo "distanceInKm(): {$distanceMethod1} km\n";
echo "distanceBetweenPoints(): {$distanceMethod2} km\n";
echo "Are they equal? " . ($distanceMethod1 === $distanceMethod2 ? 'Yes' : 'No') . "\n";
// Output: Are they equal? Yes

// Practical use case: Finding the closest airport
$airports = [
    'CDG' => ['name' => 'Charles de Gaulle Airport', 'lat' => 49.0097, 'lon' => 2.5479],
    'ORY' => ['name' => 'Orly Airport', 'lat' => 48.7262, 'lon' => 2.3652],
    'BVA' => ['name' => 'Beauvais–Tillé Airport', 'lat' => 49.4544, 'lon' => 2.1125]
];

// User's current location in Paris
$userLat = 48.8566;
$userLon = 2.3522;

function findClosestAirport($userLat, $userLon, $airports) {
    $closestAirport = null;
    $shortestDistance = PHP_FLOAT_MAX;

    foreach ($airports as $code => $airport) {
        $distance = Number::distanceInKm(
            $userLat, $userLon,
            $airport['lat'], $airport['lon'],
            1 // 1 decimal place for readability
        );

        if ($distance < $shortestDistance) {
            $shortestDistance = $distance;
            $closestAirport = [
                'code' => $code,
                'name' => $airport['name'],
                'distance' => $distance
            ];
        }
    }

    return $closestAirport;
}

$closestAirport = findClosestAirport($userLat, $userLon, $airports);
echo "\nClosest airport to your location: {$closestAirport['name']} ({$closestAirport['code']}), {$closestAirport['distance']} km away\n";

// Practical use case: Calculating area coverage
function isWithinRadius($centerLat, $centerLon, $pointLat, $pointLon, $radiusKm) {
    $distance = Number::distanceInKm($centerLat, $centerLon, $pointLat, $pointLon);
    return $distance <= $radiusKm;
}

// Check if a point is within delivery radius
$restaurantLat = 48.8738;
$restaurantLon = 2.3749;
$deliveryRadius = 5; // km

$deliveryAddresses = [
    ['name' => 'Address 1', 'lat' => 48.8566, 'lon' => 2.3522],
    ['name' => 'Address 2', 'lat' => 48.8417, 'lon' => 2.2864],
    ['name' => 'Address 3', 'lat' => 48.9032, 'lon' => 2.4659]
];

echo "\nDelivery availability check:\n";
foreach ($deliveryAddresses as $address) {
    $canDeliver = isWithinRadius(
        $restaurantLat, $restaurantLon,
        $address['lat'], $address['lon'],
        $deliveryRadius
    );

    $distance = Number::distanceInKm(
        $restaurantLat, $restaurantLon,
        $address['lat'], $address['lon'],
        1
    );

    echo "{$address['name']} is {$distance} km away. Delivery available: " .
         ($canDeliver ? 'Yes' : 'No') . "\n";
}
```

## Mathematical Operations

### greatestCommonDivisor()

```php
public function greatestCommonDivisor(
    int $first,
    int $second,
): int
```

Calculates the greatest common divisor (GCD) of two integers.

Parameters:

- `$first`: The first integer
- `$second`: The second integer

Returns the greatest common divisor.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - calculate the GCD of two numbers
$gcd1 = Number::greatestCommonDivisor(48, 18);
echo "GCD of 48 and 18: {$gcd1}\n";
// Output: GCD of 48 and 18: 6

$gcd2 = Number::greatestCommonDivisor(35, 49);
echo "GCD of 35 and 49: {$gcd2}\n";
// Output: GCD of 35 and 49: 7

// Calculating GCD for multiple pairs of numbers
$pairs = [
    [12, 8],
    [100, 75],
    [36, 48],
    [17, 23],
    [1071, 462]
];

foreach ($pairs as [$a, $b]) {
    $gcd = Number::greatestCommonDivisor($a, $b);
    echo "GCD of {$a} and {$b}: {$gcd}\n";
}
// Output:
// GCD of 12 and 8: 4
// GCD of 100 and 75: 25
// GCD of 36 and 48: 12
// GCD of 17 and 23: 1
// GCD of 1071 and 462: 21

// Handling edge cases
// GCD with zero
$gcdWithZero = Number::greatestCommonDivisor(42, 0);
echo "GCD of 42 and 0: {$gcdWithZero}\n";
// Output: GCD of 42 and 0: 42 (GCD(a,0) = |a|)

// GCD with negative numbers
$gcdWithNegative = Number::greatestCommonDivisor(-54, 24);
echo "GCD of -54 and 24: {$gcdWithNegative}\n";
// Output: GCD of -54 and 24: 6 (GCD ignores signs)

// Practical use case: Simplifying fractions
function simplifyFraction($numerator, $denominator) {
    $gcd = Number::greatestCommonDivisor($numerator, $denominator);

    return [
        'numerator' => $numerator / $gcd,
        'denominator' => $denominator / $gcd
    ];
}

$fractions = [
    [8, 12],
    [15, 25],
    [36, 48],
    [100, 80]
];

echo "\nSimplifying fractions:\n";
foreach ($fractions as [$numerator, $denominator]) {
    $simplified = simplifyFraction($numerator, $denominator);
    echo "{$numerator}/{$denominator} = {$simplified['numerator']}/{$simplified['denominator']}\n";
}
// Output:
// 8/12 = 2/3
// 15/25 = 3/5
// 36/48 = 3/4
// 100/80 = 5/4

// Practical use case: Finding common factors
function findCommonFactors($a, $b) {
    $gcd = Number::greatestCommonDivisor($a, $b);
    $factors = [];

    // Find all factors of the GCD
    for ($i = 1; $i <= $gcd; $i++) {
        if ($gcd % $i === 0) {
            $factors[] = $i;
        }
    }

    return $factors;
}

$a = 48;
$b = 60;
$commonFactors = findCommonFactors($a, $b);

echo "\nCommon factors of {$a} and {$b}: " . implode(', ', $commonFactors) . "\n";
// Output: Common factors of 48 and 60: 1, 2, 3, 4, 6, 12

// Practical use case: Checking if two numbers are coprime
function areCoprime($a, $b) {
    return Number::greatestCommonDivisor($a, $b) === 1;
}

$testPairs = [
    [15, 28],
    [12, 35],
    [18, 35],
    [17, 31]
];

echo "\nChecking for coprime numbers:\n";
foreach ($testPairs as [$a, $b]) {
    echo "{$a} and {$b} are " . (areCoprime($a, $b) ? 'coprime' : 'not coprime') . "\n";
}
// Output:
// 15 and 28 are coprime
// 12 and 35 are coprime
// 18 and 35 are coprime
// 17 and 31 are coprime
```

### leastCommonMultiple()

```php
public function leastCommonMultiple(
    int $first,
    int $second,
): int
```

Calculates the least common multiple (LCM) of two integers.

Parameters:

- `$first`: The first integer
- `$second`: The second integer

Returns the least common multiple.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - calculate the LCM of two numbers
$lcm1 = Number::leastCommonMultiple(12, 15);
echo "LCM of 12 and 15: {$lcm1}\n";
// Output: LCM of 12 and 15: 60

$lcm2 = Number::leastCommonMultiple(8, 6);
echo "LCM of 8 and 6: {$lcm2}\n";
// Output: LCM of 8 and 6: 24

// Calculating LCM for multiple pairs of numbers
$pairs = [
    [4, 6],
    [15, 25],
    [7, 11],
    [12, 18],
    [5, 7]
];

foreach ($pairs as [$a, $b]) {
    $lcm = Number::leastCommonMultiple($a, $b);
    echo "LCM of {$a} and {$b}: {$lcm}\n";
}
// Output:
// LCM of 4 and 6: 12
// LCM of 15 and 25: 75
// LCM of 7 and 11: 77
// LCM of 12 and 18: 36
// LCM of 5 and 7: 35

// Handling edge cases
// LCM with zero
try {
    $lcmWithZero = Number::leastCommonMultiple(42, 0);
    echo "LCM of 42 and 0: {$lcmWithZero}\n";
} catch (\Exception $e) {
    echo "Error calculating LCM with zero: " . $e->getMessage() . "\n";
    // LCM with zero is undefined or considered to be zero, depending on implementation
}

// LCM with negative numbers
$lcmWithNegative = Number::leastCommonMultiple(-15, 10);
echo "LCM of -15 and 10: {$lcmWithNegative}\n";
// Output: LCM of -15 and 10: 30 (LCM typically ignores signs)

// Relationship between LCM and GCD
// LCM(a,b) * GCD(a,b) = |a * b|
$a = 24;
$b = 36;
$lcm = Number::leastCommonMultiple($a, $b);
$gcd = Number::greatestCommonDivisor($a, $b);
$product = $lcm * $gcd;
$absProduct = abs($a * $b);

echo "\nLCM({$a},{$b}) = {$lcm}\n";
echo "GCD({$a},{$b}) = {$gcd}\n";
echo "LCM * GCD = {$product}\n";
echo "|{$a} * {$b}| = {$absProduct}\n";
echo "LCM * GCD = |a * b|: " . ($product === $absProduct ? 'True' : 'False') . "\n";
// Output should confirm that LCM * GCD = |a * b|

// Practical use case: Finding common time intervals
function findCommonTimeInterval($interval1, $interval2) {
    return Number::leastCommonMultiple($interval1, $interval2);
}

// Example: Two trains pass a station every 15 and 25 minutes
// When will they both pass the station at the same time?
$train1Interval = 15; // minutes
$train2Interval = 25; // minutes
$commonInterval = findCommonTimeInterval($train1Interval, $train2Interval);

echo "\nTrain 1 passes every {$train1Interval} minutes\n";
echo "Train 2 passes every {$train2Interval} minutes\n";
echo "Both trains will pass the station together every {$commonInterval} minutes\n";
// Output: Both trains will pass the station together every 75 minutes

// Practical use case: Finding the common denominator for fractions
function findCommonDenominator($fraction1, $fraction2) {
    $lcm = Number::leastCommonMultiple($fraction1['denominator'], $fraction2['denominator']);

    $fraction1Factor = $lcm / $fraction1['denominator'];
    $fraction2Factor = $lcm / $fraction2['denominator'];

    return [
        'fraction1' => [
            'numerator' => $fraction1['numerator'] * $fraction1Factor,
            'denominator' => $lcm
        ],
        'fraction2' => [
            'numerator' => $fraction2['numerator'] * $fraction2Factor,
            'denominator' => $lcm
        ],
        'common_denominator' => $lcm
    ];
}

$fraction1 = ['numerator' => 3, 'denominator' => 4];
$fraction2 = ['numerator' => 2, 'denominator' => 5];

$result = findCommonDenominator($fraction1, $fraction2);

echo "\nConverting fractions to common denominator:\n";
echo "{$fraction1['numerator']}/{$fraction1['denominator']} = {$result['fraction1']['numerator']}/{$result['fraction1']['denominator']}\n";
echo "{$fraction2['numerator']}/{$fraction2['denominator']} = {$result['fraction2']['numerator']}/{$result['fraction2']['denominator']}\n";
echo "Common denominator: {$result['common_denominator']}\n";
// Output:
// 3/4 = 15/20
// 2/5 = 8/20
// Common denominator: 20

// Practical use case: Finding LCM of multiple numbers
function findLCMOfMultipleNumbers($numbers) {
    $result = $numbers[0];

    for ($i = 1; $i < count($numbers); $i++) {
        $result = Number::leastCommonMultiple($result, $numbers[$i]);
    }

    return $result;
}

$numbers = [4, 6, 8, 10];
$lcmOfMultiple = findLCMOfMultipleNumbers($numbers);

echo "\nLCM of " . implode(', ', $numbers) . " is {$lcmOfMultiple}\n";
// Output: LCM of 4, 6, 8, 10 is 120
```

### swap()

```php
public function swap(
    mixed $first,
    mixed $second,
): array
```

Swaps two values.

Parameters:

- `$first`: The first value
- `$second`: The second value

Returns an array containing the swapped values.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - swap two integer values
$a = 5;
$b = 10;
echo "Before swap: a = {$a}, b = {$b}\n";

[$a, $b] = Number::swap($a, $b);
echo "After swap: a = {$a}, b = {$b}\n";
// Output:
// Before swap: a = 5, b = 10
// After swap: a = 10, b = 5

// Swapping string values
$firstName = "John";
$lastName = "Doe";
echo "Before swap: {$firstName} {$lastName}\n";

[$firstName, $lastName] = Number::swap($firstName, $lastName);
echo "After swap: {$firstName} {$lastName}\n";
// Output:
// Before swap: John Doe
// After swap: Doe John

// Swapping array values
$array1 = [1, 2, 3];
$array2 = [4, 5, 6];
echo "Before swap: array1 = [" . implode(", ", $array1) . "], array2 = [" . implode(", ", $array2) . "]\n";

[$array1, $array2] = Number::swap($array1, $array2);
echo "After swap: array1 = [" . implode(", ", $array1) . "], array2 = [" . implode(", ", $array2) . "]\n";
// Output:
// Before swap: array1 = [1, 2, 3], array2 = [4, 5, 6]
// After swap: array1 = [4, 5, 6], array2 = [1, 2, 3]

// Swapping object values
$obj1 = new stdClass();
$obj1->name = "Object 1";
$obj2 = new stdClass();
$obj2->name = "Object 2";
echo "Before swap: obj1->name = {$obj1->name}, obj2->name = {$obj2->name}\n";

[$obj1, $obj2] = Number::swap($obj1, $obj2);
echo "After swap: obj1->name = {$obj1->name}, obj2->name = {$obj2->name}\n";
// Output:
// Before swap: obj1->name = Object 1, obj2->name = Object 2
// After swap: obj1->name = Object 2, obj2->name = Object 1

// Practical use case: Bubble sort algorithm
function bubbleSort($arr) {
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                // Swap elements
                [$arr[$j], $arr[$j + 1]] = Number::swap($arr[$j], $arr[$j + 1]);
            }
        }
    }
    return $arr;
}

$unsortedArray = [64, 34, 25, 12, 22, 11, 90];
echo "Unsorted array: [" . implode(", ", $unsortedArray) . "]\n";

$sortedArray = bubbleSort($unsortedArray);
echo "Sorted array: [" . implode(", ", $sortedArray) . "]\n";
// Output:
// Unsorted array: [64, 34, 25, 12, 22, 11, 90]
// Sorted array: [11, 12, 22, 25, 34, 64, 90]

// Practical use case: Swapping coordinates
function swapCoordinates($point) {
    // Swap x and y coordinates
    [$point['x'], $point['y']] = Number::swap($point['x'], $point['y']);
    return $point;
}

$point = ['x' => 10, 'y' => 20];
echo "Original point: ({$point['x']}, {$point['y']})\n";

$swappedPoint = swapCoordinates($point);
echo "Swapped point: ({$swappedPoint['x']}, {$swappedPoint['y']})\n";
// Output:
// Original point: (10, 20)
// Swapped point: (20, 10)

// Practical use case: Rotating values in a circular buffer
function rotateValues($values) {
    $n = count($values);
    if ($n <= 1) {
        return $values;
    }

    $temp = $values[0];
    for ($i = 0; $i < $n - 1; $i++) {
        [$values[$i], $values[$i + 1]] = Number::swap($values[$i], $values[$i + 1]);
    }
    return $values;
}

$circularBuffer = ['A', 'B', 'C', 'D', 'E'];
echo "Original buffer: [" . implode(", ", $circularBuffer) . "]\n";

$rotatedBuffer = rotateValues($circularBuffer);
echo "Rotated buffer: [" . implode(", ", $rotatedBuffer) . "]\n";
// Output:
// Original buffer: [A, B, C, D, E]
// Rotated buffer: [B, C, D, E, A]

// Note: In PHP, you can also swap variables using list() or the shorthand [] syntax:
$x = 1;
$y = 2;
[$x, $y] = [$y, $x]; // Native PHP swap without using the function
echo "Native PHP swap: x = {$x}, y = {$y}\n";
// Output: Native PHP swap: x = 2, y = 1
```

## Type Checking

### isInt()

```php
public function isInt(
    mixed $value,
): bool
```

Checks if a value is an integer.

Parameters:

- `$value`: The value to check

Returns a boolean indicating whether the value is an integer.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - checking various values
$values = [
    42,            // integer
    -17,           // negative integer
    0,             // zero
    3.14,          // float
    '123',         // string that looks like an integer
    '123.45',      // string that looks like a float
    'hello',       // string
    true,          // boolean true
    false,         // boolean false
    null,          // null
    [],            // empty array
    new stdClass() // object
];

foreach ($values as $value) {
    $type = gettype($value);
    $isInt = Number::isInt($value);
    echo "Value: " . (is_string($value) ? "'{$value}'" : var_export($value, true)) .
         " (type: {$type}) is " . ($isInt ? 'an integer' : 'not an integer') . "\n";
}
// Output:
// Value: 42 (type: integer) is an integer
// Value: -17 (type: integer) is an integer
// Value: 0 (type: integer) is an integer
// Value: 3.14 (type: double) is not an integer
// Value: '123' (type: string) is not an integer
// Value: '123.45' (type: string) is not an integer
// Value: 'hello' (type: string) is not an integer
// Value: true (type: boolean) is not an integer
// Value: false (type: boolean) is not an integer
// Value: NULL (type: NULL) is not an integer
// Value: array () (type: array) is not an integer
// Value: stdClass::__set_state(array()) (type: object) is not an integer

// Comparing with PHP's built-in is_int() function
echo "\nComparing with PHP's is_int() function:\n";
foreach ($values as $value) {
    $valksor = Number::isInt($value);
    $php = is_int($value);
    $match = $valksor === $php ? 'Match' : 'Mismatch';
    echo "Value: " . (is_string($value) ? "'{$value}'" : var_export($value, true)) .
         " - Valksor: " . ($valksor ? 'true' : 'false') .
         ", PHP: " . ($php ? 'true' : 'false') .
         " ({$match})\n";
}
// Output should show that Number::isInt() behaves the same as PHP's is_int()

// Practical use case: Validating user input
function validateAge($age) {
    if (!Number::isInt($age)) {
        return "Age must be an integer";
    }

    if ($age < 0 || $age > 120) {
        return "Age must be between 0 and 120";
    }

    return true;
}

$userInputs = [25, '30', -5, 150, 3.5, 'twenty'];
echo "\nValidating user age inputs:\n";
foreach ($userInputs as $input) {
    $result = validateAge($input);
    echo "Input: " . (is_string($input) ? "'{$input}'" : $input) .
         " - Result: " . (is_string($result) ? $result : 'Valid') . "\n";
}
// Output:
// Input: 25 - Result: Valid
// Input: '30' - Result: Age must be an integer
// Input: -5 - Result: Age must be between 0 and 120
// Input: 150 - Result: Age must be between 0 and 120
// Input: 3.5 - Result: Age must be an integer
// Input: 'twenty' - Result: Age must be an integer

// Practical use case: Type checking in functions
function calculateSum(...$numbers) {
    $sum = 0;
    $invalidValues = [];

    foreach ($numbers as $number) {
        if (Number::isInt($number)) {
            $sum += $number;
        } else {
            $invalidValues[] = $number;
        }
    }

    return [
        'sum' => $sum,
        'invalidValues' => $invalidValues
    ];
}

$result = calculateSum(5, 10, '15', 3.14, 'hello', 20);
echo "\nSum calculation result:\n";
echo "Sum: {$result['sum']}\n";
echo "Invalid values: " . count($result['invalidValues']) . "\n";
// Output:
// Sum calculation result:
// Sum: 35
// Invalid values: 3
```

### isFloat()

```php
public function isFloat(
    mixed $value,
): bool
```

Checks if a value is a float.

Parameters:

- `$value`: The value to check

Returns a boolean indicating whether the value is a float.

Example:

```php
use Valksor\Functions\Number;

// Basic usage - checking various values
$values = [
    3.14,          // float
    -2.5,          // negative float
    0.0,           // zero as float
    42,            // integer
    0,             // zero as integer
    '3.14',        // string that looks like a float
    '42',          // string that looks like an integer
    '1.2e3',       // string with scientific notation
    'hello',       // string
    true,          // boolean true
    false,         // boolean false
    null,          // null
    [],            // empty array
    new stdClass() // object
];

foreach ($values as $value) {
    $type = gettype($value);
    $isFloat = Number::isFloat($value);
    echo "Value: " . (is_string($value) ? "'{$value}'" : var_export($value, true)) .
         " (type: {$type}) is " . ($isFloat ? 'a float' : 'not a float') . "\n";
}
// Output:
// Value: 3.14 (type: double) is a float
// Value: -2.5 (type: double) is a float
// Value: 0.0 (type: double) is a float
// Value: 42 (type: integer) is not a float
// Value: 0 (type: integer) is not a float
// Value: '3.14' (type: string) is not a float
// Value: '42' (type: string) is not a float
// Value: '1.2e3' (type: string) is not a float
// Value: 'hello' (type: string) is not a float
// Value: true (type: boolean) is not a float
// Value: false (type: boolean) is not a float
// Value: NULL (type: NULL) is not a float
// Value: array () (type: array) is not a float
// Value: stdClass::__set_state(array()) (type: object) is not a float

// Comparing with PHP's built-in is_float() function
echo "\nComparing with PHP's is_float() function:\n";
foreach ($values as $value) {
    $valksor = Number::isFloat($value);
    $php = is_float($value);
    $match = $valksor === $php ? 'Match' : 'Mismatch';
    echo "Value: " . (is_string($value) ? "'{$value}'" : var_export($value, true)) .
         " - Valksor: " . ($valksor ? 'true' : 'false') .
         ", PHP: " . ($php ? 'true' : 'false') .
         " ({$match})\n";
}
// Output should show that Number::isFloat() behaves the same as PHP's is_float()

// Practical use case: Validating price input
function validatePrice($price) {
    if (!Number::isFloat($price) && !Number::isInt($price)) {
        return "Price must be a number";
    }

    if ($price < 0) {
        return "Price cannot be negative";
    }

    return true;
}

$priceInputs = [29.99, 50, '19.99', -5.99, 0, 'free'];
echo "\nValidating price inputs:\n";
foreach ($priceInputs as $input) {
    $result = validatePrice($input);
    echo "Input: " . (is_string($input) ? "'{$input}'" : $input) .
         " - Result: " . (is_string($result) ? $result : 'Valid') . "\n";
}
// Output:
// Input: 29.99 - Result: Valid
// Input: 50 - Result: Valid
// Input: '19.99' - Result: Price must be a number
// Input: -5.99 - Result: Price cannot be negative
// Input: 0 - Result: Valid
// Input: 'free' - Result: Price must be a number

// Practical use case: Handling scientific notation
function convertScientificNotation($value) {
    if (is_string($value) && preg_match('/^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$/', $value)) {
        $floatValue = (float)$value;
        echo "Converted '{$value}' to float: {$floatValue}\n";
        return $floatValue;
    }

    if (Number::isFloat($value) || Number::isInt($value)) {
        return $value;
    }

    return null;
}

$scientificNotations = ['1.2e3', '7.5e-2', '3E4', '-2.5e+1', 'not_a_number'];
echo "\nHandling scientific notation:\n";
foreach ($scientificNotations as $notation) {
    $result = convertScientificNotation($notation);
    if ($result !== null) {
        $isFloat = Number::isFloat($result);
        $isInt = Number::isInt($result);
        $type = $isFloat ? 'float' : ($isInt ? 'integer' : 'other');
        echo "'{$notation}' converted to: {$result} (type: {$type})\n";
    } else {
        echo "'{$notation}' is not a valid number\n";
    }
}
// Output:
// Converted '1.2e3' to float: 1200
// '1.2e3' converted to: 1200 (type: float)
// Converted '7.5e-2' to float: 0.075
// '7.5e-2' converted to: 0.075 (type: float)
// Converted '3E4' to float: 30000
// '3E4' converted to: 30000 (type: float)
// Converted '-2.5e+1' to float: -25
// '-2.5e+1' converted to: -25 (type: float)
// 'not_a_number' is not a valid number

// Practical use case: Calculating average of mixed values
function calculateAverage($values) {
    $sum = 0;
    $count = 0;
    $invalidValues = [];

    foreach ($values as $value) {
        if (Number::isFloat($value) || Number::isInt($value)) {
            $sum += $value;
            $count++;
        } else {
            $invalidValues[] = $value;
        }
    }

    return [
        'average' => $count > 0 ? $sum / $count : null,
        'validCount' => $count,
        'invalidCount' => count($invalidValues),
        'invalidValues' => $invalidValues
    ];
}

$mixedValues = [10, 15.5, '20', 3.14, 'hello', true, 7];
$result = calculateAverage($mixedValues);

echo "\nAverage calculation result:\n";
echo "Average: " . ($result['average'] !== null ? $result['average'] : 'N/A') . "\n";
echo "Valid values: {$result['validCount']}\n";
echo "Invalid values: {$result['invalidCount']}\n";
// Output:
// Average calculation result:
// Average: 8.91
// Valid values: 4
// Invalid values: 3
```
