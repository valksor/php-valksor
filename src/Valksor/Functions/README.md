# Valksor Functions

[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-functions/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-functions/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-functions?branch=master)

## This repository contains these:

<table>
<tr>
<th>Repository</th>
<th>Coverage</th>
<th>Repository</th>
<th>Coverage</th>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-functions-date">php-functions-date</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-date?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-date/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-functions-handler">php-functions-handler</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-handler?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-handler/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-functions-iteration">php-functions-iteration</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-iteration?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-iteration/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-functions-latvian">php-functions-latvian</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-latvian?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-latvian/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-functions-local">php-functions-local</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-local?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-local/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-functions-memoize">php-functions-memoize</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-memoize?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-memoize/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-functions-number">php-functions-number</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-number?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-number/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-functions-pagination">php-functions-pagination</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-pagination?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-pagination/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-functions-php">php-functions-php</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-php?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-php/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-functions-preg">php-functions-preg</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-preg?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-preg/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-functions-queue">php-functions-queue</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-queue?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-queue/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-functions-sort">php-functions-sort</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-sort?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-sort/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-functions-text">php-functions-text</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-text?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-text/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-functions-web">php-functions-web</a></td>
<td><a href="https://coveralls.io/github/valksor/php-functions-web?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-functions-web/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
</table>

A comprehensive collection of PHP utility functions and helpers for various tasks including date manipulation, web operations, text processing, and more. This is a meta-package that includes all the Valksor function sub-libraries.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions
```

This will install all the function sub-libraries at once.

## Requirements

- PHP 8.4 or higher
- Various PHP extensions (curl, json, random)
- Symfony components (http-foundation, intl, process, property-access, routing, string)

## Included Sub-libraries

This meta-package includes the following function libraries:

- [valksor/php-functions-date](Date) - Date and time manipulation utilities
- [valksor/php-functions-handler](Handler) - Error and exception handling utilities
- [valksor/php-functions-iteration](Iteration) - Array and collection iteration utilities
- [valksor/php-functions-latvian](Latvian) - Latvian language specific utilities
- [valksor/php-functions-local](Local) - Localization and internationalization utilities
- [valksor/php-functions-memoize](Memoize) - Function result caching utilities
- [valksor/php-functions-number](Number) - Number manipulation and formatting utilities
- [valksor/php-functions-pagination](Pagination) - Pagination utilities for arrays and collections
- [valksor/php-functions-php](Php) - PHP language enhancement utilities
- [valksor/php-functions-preg](Preg) - Regular expression utilities
- [valksor/php-functions-queue](Queue) - FIFO queue implementation for managing collections of items
- [valksor/php-functions-sort](Sort) - Sorting algorithms and utilities
- [valksor/php-functions-text](Text) - Text processing and manipulation utilities
- [valksor/php-functions-web](Web) - Web-related utilities for HTTP requests, URLs, etc.

Each sub-library can also be installed individually if you only need specific functionality.

## Usage

Each sub-library has its own usage instructions. Please refer to the README.md file in each sub-library's directory for specific usage examples.

Generally, there are two ways to use these libraries:

1. Via the Functions class provided by each sub-library
2. By directly using the traits in your own classes


## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to function libraries:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-function`)
3. Implement your function following existing patterns
4. Add comprehensive tests
5. Ensure all tests pass and code style is correct
6. Submit a pull request

### Creating New Function Libraries

When adding new function libraries:

1. Create namespace under `Valksor\Functions\{Name}`
2. Implement Functions class and individual traits
3. Add comprehensive test coverage in `tests/Functions/{Name}/`
4. Create composer.json with proper dependencies
5. Update meta-package composer.json to include new library
6. Update documentation with examples

## Security

If you discover any security-related issues, please email us at security@valksor.dev instead of using the issue tracker.

For security policy and vulnerability reporting guidelines, please see our [Security Policy](SECURITY.md).

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-functions`
- **Individual Library Support**: Each library has dedicated documentation

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[PHP Community](https://www.php.net)** - Language and ecosystem support
- **[Symfony Components](https://symfony.com/components)** - Many functions utilize Symfony components
- **[Valksor Project](https://github.com/valksor)** - Part of the larger Valksor PHP ecosystem

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/php-valksor](https://github.com/valksor/php-valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- Symfony bundle for easy configuration
- And much more

If you find these function components useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
