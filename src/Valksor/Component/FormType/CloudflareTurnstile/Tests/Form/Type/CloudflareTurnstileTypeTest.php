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

// PACKAGE: Verifies form configuration, view building, and option validation.

namespace Valksor\Component\FormType\CloudflareTurnstile\Tests\Form\Type;

use Closure;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Throwable;
use Valksor\Component\FormType\CloudflareTurnstile\Constraints\CloudflareTurnstile;
use Valksor\Component\FormType\CloudflareTurnstile\Form\Type\CloudflareTurnstileType;
use Valksor\Component\FormType\CloudflareTurnstile\Service\CloudflareTurnstileRegistry;

final class CloudflareTurnstileTypeTest extends TestCase
{
    private CloudflareTurnstileRegistry $registry;
    private CloudflareTurnstileType $type;

    public function testBuildViewSetsKeyAndTypeVariables(): void
    {
        $view = new FormView();
        $form = $this->createStub(FormInterface::class);

        $this->type->buildView($view, $form, ['type' => 'default']);

        $this->assertSame('site_key_default', $view->vars['key']);
        $this->assertSame('default', $view->vars['turnstile_type']);
    }

    public function testBuildViewWithDifferentType(): void
    {
        $view = new FormView();
        $form = $this->createStub(FormInterface::class);

        $this->type->buildView($view, $form, ['type' => 'contact']);

        $this->assertSame('site_key_contact', $view->vars['key']);
        $this->assertSame('contact', $view->vars['turnstile_type']);
    }

    public function testConfigureOptionsSetsDefaults(): void
    {
        $resolver = new OptionsResolver();
        $this->type->configureOptions($resolver);

        $options = $resolver->resolve(['type' => 'default']);

        $this->assertFalse($options['mapped']);
        $this->assertSame('default', $options['type']);
        $this->assertInstanceOf(Closure::class, $options['constraints']);
    }

    public function testConfigureOptionsThrowsExceptionForNonExistentType(): void
    {
        $resolver = new OptionsResolver();
        $this->type->configureOptions($resolver);

        $exceptionThrown = false;
        $exceptionMessage = '';

        try {
            $resolver->resolve(['type' => 'nonexistent_type']);
        } catch (Throwable $e) {
            $exceptionThrown = true;
            $exceptionMessage = $e->getMessage();
        }

        $this->assertTrue($exceptionThrown, 'Expected exception was not thrown for non-existent type');
        $this->assertStringContainsString('Invalid Cloudflare Turnstile type "nonexistent_type"', $exceptionMessage);
        $this->assertStringContainsString('Available types:', $exceptionMessage);
    }

    public function testConfigureOptionsThrowsExceptionWhenTypeNotSet(): void
    {
        $resolver = new OptionsResolver();
        $this->type->configureOptions($resolver);

        $exceptionThrown = false;
        $exceptionMessage = '';

        try {
            $resolver->resolve([]);
        } catch (Throwable $e) {
            $exceptionThrown = true;
            $exceptionMessage = $e->getMessage();
        }

        $this->assertTrue($exceptionThrown, 'Expected exception was not thrown when type is not set');
        $this->assertStringContainsString('The "type" option is required', $exceptionMessage);
    }

    public function testConstraintClosureReturnsCorrectConstraint(): void
    {
        $resolver = new OptionsResolver();
        $this->type->configureOptions($resolver);

        $options = $resolver->resolve(['type' => 'contact']);

        /** @var Closure $constraintsClosure */
        $constraintsClosure = $options['constraints'];

        $constraint = $constraintsClosure($options);

        $this->assertInstanceOf(CloudflareTurnstile::class, $constraint);
        $this->assertSame('contact', $constraint->type);
    }

    public function testGetBlockPrefixReturnsCorrectPrefix(): void
    {
        $this->assertSame('valksor_form_type_cloudflare_turnstile', $this->type->getBlockPrefix());
    }

    public function testGetParentReturnsTextType(): void
    {
        $this->assertSame(TextType::class, $this->type->getParent());
    }

    protected function setUp(): void
    {
        $parameterBag = new ParameterBag([
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

        $this->registry = new CloudflareTurnstileRegistry($parameterBag);
        $this->type = new CloudflareTurnstileType($this->registry);
    }
}
