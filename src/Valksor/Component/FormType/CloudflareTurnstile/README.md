# Valksor Component: FormType CloudflareTurnstile

[![valksor](https://badgen.net/static/org/valksor/green)](https://github.com/valksor)
[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-form-type-cloudflare-turnstile/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-form-type-cloudflare-turnstile/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-form-type-cloudflare-turnstile?branch=master)
[![php](https://badgen.net/static/php/>=8.4/purple)](https://www.php.net/releases/8.4/en.php)

A Symfony Form type providing Cloudflare Turnstile CAPTCHA integration with server-side validation. Privacy-focused alternative to reCAPTCHA.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-form-type-cloudflare-turnstile
```

## Requirements

- PHP 8.4 or higher
- Symfony Form Component (>=6.4)
- Symfony HttpClient
- Symfony Validator
- Cloudflare Turnstile sitekey and secret

## Usage

There are two ways to use this package: via the form type class or by configuring the registry.

### Using the Form Type

Add the Turnstile field to your form with the required `type` option:

```php
use Valksor\Component\FormType\CloudflareTurnstile\Form\Type\CloudflareTurnstileType;

$form = $this->createFormBuilder()
    ->add('turnstile', CloudflareTurnstileType::class, [
        'type' => 'managed',
    ])
    ->getForm();
```

### Configuration

Configure sitekeys and secrets in `config/packages/valksor.yaml`:

```yaml
valksor:
    cloudflare_turnstile:
        types:
            managed:
                sitekey: 'your_sitekey_managed'
                secret: 'your_secret_managed'
            non_interactive:
                sitekey: 'your_sitekey_non_interactive'
                secret: 'your_secret_non_interactive'
```

## Features

### Supported Types

Managed by `CloudflareTurnstileRegistry`. Add types in config.

| Type | Description |
|------|-------------|
| `managed` | Standard interactive widget |
| `non_interactive` | Non-interactive verification |
| `invisible` | Invisible background verification |

### Server Validation

Automatic via `CloudflareTurnstile` constraint. Validates token against Cloudflare API.

### Frontend Rendering

Uses `fields.html.twig` template. Turnstile widget renders automatically with sitekey.

### Required Options

| Option | Type | Description |
|--------|------|-------------|
| `type` | `string` | Required. Must match configured types (managed, non_interactive, etc.) |
| `mapped` | `bool` | Default: `false`. Field is not mapped to entity |

## Testing

Run the test suite for CloudflareTurnstile:

```bash
# Run all CloudflareTurnstile tests
bin/unit Valksor/Component/FormType/CloudflareTurnstile

# Run tests with coverage
vendor/bin/phpunit src/Valksor/Component/FormType/CloudflareTurnstile --coverage-text
```

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to CloudflareTurnstile:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/turnstile-improvement`)
3. Implement your changes following existing patterns
4. Add comprehensive tests
5. Ensure all tests pass and code style is correct
6. Submit a pull request

## Security

If you discover any security-related issues, please email us at packages@valksor.com instead of using the issue tracker.

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/orgs/valksor/discussions/categories/php-valksor) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-form-type-cloudflare-turnstile`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Symfony Form](https://symfony.com/doc/current/forms.html)** - Form component foundation
- **[Cloudflare Turnstile](https://developers.cloudflare.com/turnstile/)** - CAPTCHA service
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

If you find this CloudflareTurnstile component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
