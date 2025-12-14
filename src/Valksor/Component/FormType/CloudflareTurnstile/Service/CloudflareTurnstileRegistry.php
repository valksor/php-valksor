<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Davis Zalitis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// PACKAGE: Manages dynamic selection of site and secret keys.

namespace Valksor\Component\FormType\CloudflareTurnstile\Service;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use function sprintf;

/**
 * Registry for managing Cloudflare Turnstile configurations.
 */
final class CloudflareTurnstileRegistry
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    /**
     * Get all available configuration types.
     *
     * @return list<string>
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->getTypes());
    }

    /**
     * Get secret key for the specified type.
     */
    public function getSecretKey(
        string $type,
    ): string {
        $types = $this->getTypes();

        return $types[$type]['secret_key'] ?? throw new InvalidArgumentException(sprintf('Cloudflare Turnstile configuration "%s" not found. Available types: %s', $type, implode(', ', array_keys($types))));
    }

    /**
     * Get site key for the specified type.
     */
    public function getSiteKey(
        string $type,
    ): string {
        $types = $this->getTypes();

        return $types[$type]['site_key'] ?? throw new InvalidArgumentException(sprintf('Cloudflare Turnstile configuration "%s" not found. Available types: %s', $type, implode(', ', array_keys($types))));
    }

    /**
     * Check if a configuration type exists.
     */
    public function hasType(
        string $type,
    ): bool {
        return isset($this->getTypes()[$type]);
    }

    /**
     * Get all types from configuration.
     *
     * @return array<string, array{site_key: string, secret_key: string}>
     */
    private function getTypes(): array
    {
        return $this->parameterBag->get('valksor.form_type.cloudflare_turnstile.types', []);
    }
}
