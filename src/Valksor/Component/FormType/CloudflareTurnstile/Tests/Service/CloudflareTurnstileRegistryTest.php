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

// PACKAGE: Verifies site key, secret key retrieval and type validation.

namespace Valksor\Component\FormType\CloudflareTurnstile\Tests\Service;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Valksor\Component\FormType\CloudflareTurnstile\Service\CloudflareTurnstileRegistry;

final class CloudflareTurnstileRegistryTest extends TestCase
{
    private ParameterBag $parameterBag;
    private CloudflareTurnstileRegistry $registry;

    public function testGetAvailableTypesReturnsConfiguredTypes(): void
    {
        $this->assertSame(['default', 'contact'], $this->registry->getAvailableTypes());
    }

    public function testGetAvailableTypesReturnsEmptyArrayWhenNoTypesConfigured(): void
    {
        $parameterBag = new ParameterBag([
            'valksor.form_type.cloudflare_turnstile.types' => [],
        ]);
        $registry = new CloudflareTurnstileRegistry($parameterBag);

        $this->assertSame([], $registry->getAvailableTypes());
    }

    public function testGetSecretKeyReturnsCorrectKey(): void
    {
        $this->assertSame('secret_key_default', $this->registry->getSecretKey('default'));
        $this->assertSame('secret_key_contact', $this->registry->getSecretKey('contact'));
    }

    public function testGetSecretKeyThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cloudflare Turnstile configuration "invalid" not found. Available types: default, contact');

        $this->registry->getSecretKey('invalid');
    }

    public function testGetSiteKeyReturnsCorrectKey(): void
    {
        $this->assertSame('site_key_default', $this->registry->getSiteKey('default'));
        $this->assertSame('site_key_contact', $this->registry->getSiteKey('contact'));
    }

    public function testGetSiteKeyThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cloudflare Turnstile configuration "nonexistent" not found. Available types: default, contact');

        $this->registry->getSiteKey('nonexistent');
    }

    public function testHasTypeReturnsFalseForNonExistingType(): void
    {
        $this->assertFalse($this->registry->hasType('nonexistent'));
        $this->assertFalse($this->registry->hasType(''));
    }

    public function testHasTypeReturnsTrueForExistingType(): void
    {
        $this->assertTrue($this->registry->hasType('default'));
        $this->assertTrue($this->registry->hasType('contact'));
    }

    protected function setUp(): void
    {
        $this->parameterBag = new ParameterBag([
            'valksor.form_type.cloudflare_turnstile.types' => [
                'default' => [
                    'site_key' => 'site_key_default',
                    'secret_key' => 'secret_key_default',
                ],
                'contact' => [
                    'site_key' => 'site_key_contact',
                    'secret_key' => 'secret_key_contact',
                ],
            ],
        ]);

        $this->registry = new CloudflareTurnstileRegistry($this->parameterBag);
    }
}
