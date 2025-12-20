# Valksor Component: FormType

[![valksor](https://badgen.net/static/org/valksor/green)](https://github.com/valksor)
[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-form-type/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-form-type/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-form-type?branch=master)
[![php](https://badgen.net/static/php/>=8.4/purple)](https://www.php.net/releases/8.4/en.php)

## This repository contains these:

<table>
<tr>
<th>Repository</th>
<th>Coverage</th>
<th>Repository</th>
<th>Coverage</th>
</tr>
<tr>
<td><a href="https://github.com/valksor/php-form-type-honey-pot">php-form-type-honey-pot</a></td>
<td><a href="https://coveralls.io/github/valksor/php-form-type-honey-pot?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-form-type-honey-pot/badge.svg?branch=master" alt="Coverage"></a></td>
<td><a href="https://github.com/valksor/php-form-type-cloudflare-turnstile">php-form-type-cloudflare-turnstile</a></td>
<td><a href="https://coveralls.io/github/valksor/php-form-type-cloudflare-turnstile?branch=master"><img src="https://coveralls.io/repos/github/valksor/php-form-type-cloudflare-turnstile/badge.svg?branch=master" alt="Coverage"></a></td>
</tr>
</table>

A collection of Symfony Form Types and Extensions providing spam protection through honeypot fields and Cloudflare Turnstile CAPTCHA integration. This is a meta-package that includes all the Valksor form type sub-libraries.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-form-type
```

This will install all the form type sub-libraries at once.

## Requirements

- PHP 8.4 or higher
- Symfony Form Component (>=6.4)
- Symfony HttpFoundation
- Symfony HttpClient (for Turnstile validation)
- Symfony Validator

## Included Sub-libraries

This meta-package includes the following form type libraries:

- [valksor/php-form-type-honey-pot](HoneyPot) - Form extension adding honeypot trap for bot detection
- [valksor/php-form-type-cloudflare-turnstile](CloudflareTurnstile) - Form type for Cloudflare Turnstile with server-side validation

Each sub-library can also be installed individually if you only need specific functionality.

## Usage

Each sub-library has its own usage instructions. Please refer to the README.md file in each sub-library's directory for specific usage examples.

Generally, components are auto-discovered when using the Valksor bundle:

1. Add to `config/bundles.php`:

```php
Valksor\Bundle\ValksorBundle::class => ['all' => true],
```

2. Configure via `config/packages/valksor.yaml` if needed.

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to form type libraries:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-form-type`)
3. Implement your form type following existing patterns
4. Add comprehensive tests
5. Ensure all tests pass and code style is correct
6. Submit a pull request

### Creating New Form Type Libraries

When adding new form type libraries:

1. Create namespace under `Valksor\Component\FormType\{Name}`
2. Implement Form Type or Extension class
3. Add comprehensive test coverage in `Tests/`
4. Create composer.json with proper dependencies
5. Update meta-package composer.json to include new library
6. Update documentation with examples

## Security

If you discover any security-related issues, please email us at packages@valksor.com instead of using the issue tracker.

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/orgs/valksor/discussions/categories/php-valksor) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-form-type`
- **Individual Library Support**: Each library has dedicated documentation

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Symfony Form](https://symfony.com/doc/current/forms.html)** - Form component foundation
- **[Cloudflare Turnstile](https://developers.cloudflare.com/turnstile/)** - CAPTCHA service integration
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

If you find these form type components useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
