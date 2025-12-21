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

// PACKAGE: Verifies constraint properties and attribute configuration.

namespace Valksor\Component\FormType\CloudflareTurnstile\Tests\Constraints;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Validator\Constraint;
use Valksor\Component\FormType\CloudflareTurnstile\Constraints\CloudflareTurnstile;

final class CloudflareTurnstileTest extends TestCase
{
    public function testConstraintExtendsSymfonyConstraint(): void
    {
        $constraint = new CloudflareTurnstile();

        $this->assertInstanceOf(Constraint::class, $constraint);
    }

    public function testConstraintIsAttribute(): void
    {
        $reflection = new ReflectionClass(CloudflareTurnstile::class);
        $attributes = $reflection->getAttributes();

        $this->assertNotEmpty($attributes);

        $attributeNames = array_map(
            static fn ($attr) => $attr->getName(),
            $attributes,
        );

        $this->assertContains('Attribute', $attributeNames);
    }

    public function testDefaultMessageValues(): void
    {
        $constraint = new CloudflareTurnstile();

        $this->assertSame('invalid_turnstile', $constraint->message);
        $this->assertSame('turnstile_configuration_not_found', $constraint->notFoundMessage);
    }

    public function testGroupsCanBeSetViaConstructor(): void
    {
        $constraint = new CloudflareTurnstile(type: 'default', groups: ['registration']);

        $this->assertContains('registration', $constraint->groups);
    }

    public function testPayloadCanBeSetViaConstructor(): void
    {
        $payload = ['severity' => 'high'];
        $constraint = new CloudflareTurnstile(type: 'default', payload: $payload);

        $this->assertSame($payload, $constraint->payload);
    }

    public function testTypeCanBeSetViaConstructor(): void
    {
        $constraint = new CloudflareTurnstile(type: 'contact');

        $this->assertSame('contact', $constraint->type);
    }

    public function testTypeIsNullByDefault(): void
    {
        $constraint = new CloudflareTurnstile();

        $this->assertNull($constraint->type);
    }
}
