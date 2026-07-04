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

// PACKAGE: Verifies honeypot field addition and bot detection logic.

namespace Valksor\Component\FormType\HoneyPot\Tests\Form\Extension;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Valksor\Component\FormType\HoneyPot\Form\Extension\HoneyPotExtension;

use function ini_get;
use function is_callable;

final class HoneyPotExtensionTest extends TestCase
{
    private HoneyPotExtension $extension;
    private RequestStack $requestStack;

    public function testBuildFormAddsCustomFieldName(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with(
                'custom_honeypot',
                TextType::class,
                $this->anything(),
            )
            ->willReturnSelf();

        $builder->expects($this->once())
            ->method('addEventListener');

        $this->extension->buildForm($builder, [
            'honeypot' => true,
            'honeypot_field_name' => 'custom_honeypot',
            'honeypot_message' => 'Bot detected',
        ]);
    }

    public function testBuildFormAddsHoneypotFieldWhenEnabled(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with(
                'website',
                TextType::class,
                [
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'class' => 'hidden',
                        'tabindex' => '-1',
                        'autocomplete' => 'off',
                    ],
                ],
            )
            ->willReturnSelf();

        $builder->expects($this->once())
            ->method('addEventListener')
            ->with(FormEvents::PRE_SUBMIT, $this->callback(static fn ($arg) => is_callable($arg)));

        $this->extension->buildForm($builder, [
            'honeypot' => true,
            'honeypot_field_name' => 'website',
            'honeypot_message' => 'Bot detected',
        ]);
    }

    public function testBuildFormDoesNothingWhenHoneypotDisabled(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->never())
            ->method('add');
        $builder->expects($this->never())
            ->method('addEventListener');

        $this->extension->buildForm($builder, ['honeypot' => false]);
    }

    public function testConfigureOptionsAllowsCustomValues(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);

        $options = $resolver->resolve([
            'honeypot' => true,
            'honeypot_field_name' => 'custom_field',
            'honeypot_message' => 'Custom bot message',
        ]);

        $this->assertTrue($options['honeypot']);
        $this->assertSame('custom_field', $options['honeypot_field_name']);
        $this->assertSame('Custom bot message', $options['honeypot_message']);
    }

    public function testConfigureOptionsSetsDefaults(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);

        $options = $resolver->resolve([]);

        $this->assertFalse($options['honeypot']);
        $this->assertSame('website', $options['honeypot_field_name']);
        $this->assertSame('This form should not be submitted by bots.', $options['honeypot_message']);
    }

    public function testGetExtendedTypesReturnsFormType(): void
    {
        $types = HoneyPotExtension::getExtendedTypes();

        $this->assertContains(FormType::class, $types);
    }

    public function testPreSubmitListenerDoesNothingWhenDataIsNotArray(): void
    {
        $listenerCallback = null;

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnSelf();
        $builder->expects($this->once())
            ->method('addEventListener')
            ->with(FormEvents::PRE_SUBMIT, $this->callback(static function ($callback) use (&$listenerCallback) {
                $listenerCallback = $callback;

                return true;
            }));

        $this->extension->buildForm($builder, [
            'honeypot' => true,
            'honeypot_field_name' => 'website',
            'honeypot_message' => 'Bot detected',
        ]);

        $form = $this->createStub(FormInterface::class);
        $event = new FormEvent($form, 'string data');

        $listenerCallback($event);

        $this->assertSame('string data', $event->getData());
    }

    public function testPreSubmitListenerDoesNothingWhenHoneypotEmpty(): void
    {
        $listenerCallback = null;

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnSelf();
        $builder->expects($this->once())
            ->method('addEventListener')
            ->with(FormEvents::PRE_SUBMIT, $this->callback(static function ($callback) use (&$listenerCallback) {
                $listenerCallback = $callback;

                return true;
            }));

        $this->extension->buildForm($builder, [
            'honeypot' => true,
            'honeypot_field_name' => 'website',
            'honeypot_message' => 'Bot detected',
        ]);

        $form = $this->createStub(FormInterface::class);
        $event = new FormEvent($form, ['name' => 'John', 'website' => '']);

        $listenerCallback($event);

        $this->assertSame(['name' => 'John', 'website' => ''], $event->getData());
    }

    public function testPreSubmitListenerHandlesNullRequest(): void
    {
        $listenerCallback = null;

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnSelf();
        $builder->expects($this->once())
            ->method('addEventListener')
            ->with(FormEvents::PRE_SUBMIT, $this->callback(static function ($callback) use (&$listenerCallback) {
                $listenerCallback = $callback;

                return true;
            }));

        $this->requestStack->method('getCurrentRequest')
            ->willReturn(null);

        $this->extension->buildForm($builder, [
            'honeypot' => true,
            'honeypot_field_name' => 'website',
            'honeypot_message' => 'Bot detected!',
        ]);

        $form = $this->createStub(FormInterface::class);
        $event = new FormEvent($form, ['name' => 'Bot', 'website' => 'http://spam.com']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bot detected!');

        $listenerCallback($event);
    }

    public function testPreSubmitListenerThrowsExceptionWhenHoneypotFilled(): void
    {
        $listenerCallback = null;

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnSelf();
        $builder->expects($this->once())
            ->method('addEventListener')
            ->with(FormEvents::PRE_SUBMIT, $this->callback(static function ($callback) use (&$listenerCallback) {
                $listenerCallback = $callback;

                return true;
            }));

        $request = new Request();
        $request->headers->set('User-Agent', 'TestBot/1.0');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $this->requestStack->method('getCurrentRequest')
            ->willReturn($request);

        $this->extension->buildForm($builder, [
            'honeypot' => true,
            'honeypot_field_name' => 'website',
            'honeypot_message' => 'Bot detected!',
        ]);

        $form = $this->createStub(FormInterface::class);
        $event = new FormEvent($form, ['name' => 'Bot', 'website' => 'http://spam.com']);

        $exceptionThrown = false;
        $exceptionMessage = '';

        $oldErrorLog = ini_get('error_log');
        ini_set('error_log', '/dev/null');

        try {
            $listenerCallback($event);
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
            $exceptionMessage = $e->getMessage();
        } finally {
            ini_set('error_log', $oldErrorLog);
        }

        $this->assertTrue($exceptionThrown, 'Expected InvalidArgumentException was not thrown');
        $this->assertSame('Bot detected!', $exceptionMessage);
    }

    protected function setUp(): void
    {
        $this->requestStack = $this->createStub(RequestStack::class);
        $this->extension = new HoneyPotExtension($this->requestStack);
    }
}
