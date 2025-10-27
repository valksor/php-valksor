# Valksor Component: SpxProfiler

A Symfony profiler data collector for the SPX PHP profiler, which collects and displays profiling data for PHP applications.

## Installation

Install the package via Composer:

```bash
composer require valksor/spx-profiler
```

## Requirements

- PHP 8.4 or higher
- SPX PHP extension installed

## Usage

The SpxProfiler component provides a data collector for the Symfony web profiler that integrates with the SPX PHP profiler. It allows you to view detailed profiling information for your PHP applications directly in the Symfony web profiler.

### Basic Setup

1. Install the SPX PHP extension (https://github.com/NoiseByNorthwest/php-spx)
2. Enable the extension in your php.ini:

```ini
extension=spx.so
spx.http_enabled=1
spx.http_key=your_secret_key
```

3. Register the component in your Symfony application:

```php
// config/bundles.php
return [
    // ...
    Valksor\Bundle\ValksorBundle::class => ['all' => true],
    // ...
];
```

4. Configure the component in your Symfony application:

```yaml
# config/packages/valksor.yaml
valksor:
    components:
        spx_profiler: ~
```

### Viewing Profiling Data

Once the component is installed and configured, you can view profiling data in the Symfony web profiler:

1. Enable SPX profiling for a request by adding the SPX_KEY parameter to the URL or setting the SPX_ENABLED cookie to 1
2. Make a request to your application
3. Open the Symfony web profiler for that request
4. Click on the SPX tab to view the profiling data

### Features

The SpxProfiler component provides the following features:

- Integration with the Symfony web profiler
- Display of SPX profiling data in a dedicated tab
- Automatic matching of SPX reports with Symfony profiler requests
- Display of detailed metrics including:
  - Wall time
  - Memory usage
  - Enabled metrics
  - Recorded calls
- Links to the full SPX report for more detailed analysis

### Configuration Options

The SPX PHP extension provides several configuration options that can be set in your php.ini:

- `spx.http_enabled`: Enable HTTP interface (default: 0)
- `spx.http_key`: Secret key for HTTP interface
- `spx.http_ip_whitelist`: IP whitelist for HTTP interface
- `spx.data_dir`: Directory for SPX data files (default: /tmp/spx)

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/php-valksor](https://github.com/valksor/php-valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- API Platform integrations
- Symfony bundle for easy configuration
- And much more

If you find this SpxProfiler component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
