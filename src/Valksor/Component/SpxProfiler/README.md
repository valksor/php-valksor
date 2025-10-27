# Valksor Component: SpxProfiler

[![Latest Version on Packagist](https://img.shields.io/packagist/v/valksor/php-spx-profiler.svg)](https://packagist.org/packages/valksor/php-spx-profiler)
[![Total Downloads](https://img.shields.io/packagist/dt/valksor/php-spx-profiler.svg)](https://packagist.org/packages/valksor/php-spx-profiler)
[![License](https://img.shields.io/packagist/l/valksor/php-spx-profiler.svg)](LICENSE)
[![PHP Version Require](https://img.shields.io/packagist/require-v/valksor/php-spx-profiler/php)](https://packagist.org/packages/valksor/php-spx-profiler)

A Symfony profiler data collector for the SPX PHP profiler, which collects and displays detailed profiling data for PHP applications. This component integrates SPX profiling directly into the Symfony web profiler for easy access to performance metrics during development.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-spx-profiler
```

## Requirements

- **PHP 8.4 or higher**
- **SPX PHP extension** installed and configured
- **Symfony Framework** (7.2.0 or higher)
- **Valksor Bundle** for automatic configuration

## Basic Usage

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

## Features

The SpxProfiler component provides comprehensive profiling integration for Symfony applications:

- **Symfony Profiler Integration**: Seamless integration with Symfony's web profiler toolbar
- **SPX Data Collection**: Automatic collection and display of detailed SPX profiling metrics
- **Real-time Performance Metrics**: Wall time, memory usage, call graphs, and execution traces
- **Request Matching**: Automatic matching of SPX reports with Symfony profiler requests
- **Configurable Profiling**: Support for different SPX modes and data collection options
- **Development Workflow**: Streamlined profiling setup for development environments

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


## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to SpxProfiler component:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/spx-enhancement`)
3. Implement your enhancement following existing patterns
4. Add comprehensive tests for new functionality
5. Ensure all tests pass and code style is correct
6. Submit a pull request

### Adding New Profiling Features

When adding new profiling functionality:

1. Extend the data collector to capture additional metrics
2. Update Twig templates for displaying new data
3. Add configuration options for new features
4. Test integration with SPX extension
5. Update documentation with examples

## Security

If you discover any security-related issues, please email us at security@valksor.dev instead of using the issue tracker.

For security policy and vulnerability reporting guidelines, please see our [Security Policy](SECURITY.md).

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-spx-profiler`
- **SPX Extension**: [SPX GitHub](https://github.com/NoiseByNorthwest/php-spx) for extension-specific issues

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[SPX Author](https://github.com/NoiseByNorthwest)** - Creator of the SPX PHP profiler extension
- **[Symfony Profiler Team](https://symfony.com/doc/current/profiler.html)** - Profiler framework and best practices inspiration
- **[Valksor Project](https://github.com/valksor)** - Part of the larger Valksor PHP ecosystem

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
