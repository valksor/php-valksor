# Valksor Component: FormType HoneyPot

[![valksor](https://badgen.net/static/org/valksor/green)](https://github.com/valksor)
[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-form-type-honey-pot/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-form-type-honey-pot/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-form-type-honey-pot?branch=master)
[![php](https://badgen.net/static/php/>=8.4/purple)](https://www.php.net/releases/8.4/en.php)

A Symfony Form extension that automatically adds an invisible honeypot spam trap field to your forms. If bots fill the field, submission is blocked and logged.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-form-type-honey-pot
```

## Requirements

- PHP 8.4 or higher
- Symfony Form Component (>=6.4)
- Symfony HttpFoundation
- Symfony RequestStack

## Usage

There are two ways to use this package: via form options or by directly configuring the extension.

### Using Form Options

The extension automatically applies to any `FormType` (root forms) when the `honeypot` option is enabled:

```php
use Symfony\Component\Form\Extension\Core\Type\FormType;

// Enable honeypot on a form
$builder = $this->createFormBuilder([], ['honeypot' => true]);
$form = $builder->getForm();
```

### Customizing Options

You can customize the honeypot field name and error message:

```php
$form = $this->createFormBuilder([], [
    'honeypot' => true,
    'honeypot_field_name' => 'website',
    'honeypot_message' => 'Spam detected!',
])->getForm();
```

## Features

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `honeypot` | `bool` | `false` | Enable/disable honeypot |
| `honeypot_field_name` | `string` | `'website'` | Name of the honeypot field |
| `honeypot_message` | `string` | `'This form should not be submitted by bots.'` | Error message on bot detection |

### How It Works

1. Adds a hidden text field (e.g. `website`) with `class="hidden"`, `tabindex="-1"`, `autocomplete="off"`
2. On `PRE_SUBMIT`, checks if field is filled
3. If filled, logs IP/User-Agent and throws `InvalidArgumentException` with custom message
4. Legitimate users ignore the field

### Twig Template

Uses `fields.html.twig` for rendering the honeypot field.

## Testing

Run the test suite for HoneyPot:

```bash
# Run all HoneyPot tests
bin/unit Valksor/Component/FormType/HoneyPot

# Run tests with coverage
vendor/bin/phpunit src/Valksor/Component/FormType/HoneyPot --coverage-text
```

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to HoneyPot:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/honeypot-improvement`)
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
- **Stack Overflow**: Use tag `valksor-php-form-type-honey-pot`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Symfony Form](https://symfony.com/doc/current/forms.html)** - Form component foundation
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

If you find this HoneyPot component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
