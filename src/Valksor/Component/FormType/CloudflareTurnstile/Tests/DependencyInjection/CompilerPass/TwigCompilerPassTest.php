<?php declare(strict_types = 1);

// PACKAGE: Tests for CloudflareTurnstile TwigCompilerPass.
// PACKAGE: Verifies Twig path and form resources registration.

namespace Valksor\Component\FormType\CloudflareTurnstile\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Valksor\Component\FormType\CloudflareTurnstile\DependencyInjection\CompilerPass\TwigCompilerPass;

final class TwigCompilerPassTest extends TestCase
{
    private TwigCompilerPass $compilerPass;

    public function testProcessDoesNothingWhenTwigFormResourcesParameterMissing(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasParameter')
            ->with('twig.form.resources')
            ->willReturn(false);

        $container->expects($this->never())
            ->method('getDefinition');

        $this->compilerPass->process($container);
    }

    public function testProcessDoesNothingWhenTwigLoaderDefinitionMissing(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasParameter')
            ->with('twig.form.resources')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('hasDefinition')
            ->with('twig.loader.native_filesystem')
            ->willReturn(false);

        $container->expects($this->never())
            ->method('getDefinition');

        $this->compilerPass->process($container);
    }

    public function testProcessAddsTwigPathAndFormResources(): void
    {
        $definition = $this->createMock(Definition::class);
        $definition->expects($this->once())
            ->method('addMethodCall')
            ->with(
                'addPath',
                $this->callback(function (array $args) {
                    return str_contains($args[0], 'Resources/views')
                        && $args[1] === 'ValksorFTCloudflareTurnstile';
                }),
            );

        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasParameter')
            ->with('twig.form.resources')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('hasDefinition')
            ->with('twig.loader.native_filesystem')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('getDefinition')
            ->with('twig.loader.native_filesystem')
            ->willReturn($definition);

        $container->expects($this->once())
            ->method('getParameter')
            ->with('twig.form.resources')
            ->willReturn(['@SomeBundle/form.html.twig']);

        $container->expects($this->once())
            ->method('setParameter')
            ->with(
                'twig.form.resources',
                $this->callback(function (array $resources) {
                    return $resources[0] === '@ValksorFTCloudflareTurnstile/fields.html.twig'
                        && $resources[1] === '@SomeBundle/form.html.twig';
                }),
            );

        $this->compilerPass->process($container);
    }

    public function testProcessHandlesEmptyFormResources(): void
    {
        $definition = $this->createMock(Definition::class);
        $definition->expects($this->once())
            ->method('addMethodCall');

        $container = $this->createMock(ContainerBuilder::class);
        $container->method('hasParameter')->willReturn(true);
        $container->method('hasDefinition')->willReturn(true);
        $container->method('getDefinition')->willReturn($definition);
        $container->method('getParameter')->willReturn(null);

        $container->expects($this->once())
            ->method('setParameter')
            ->with(
                'twig.form.resources',
                ['@ValksorFTCloudflareTurnstile/fields.html.twig'],
            );

        $this->compilerPass->process($container);
    }

    protected function setUp(): void
    {
        $this->compilerPass = new TwigCompilerPass();
    }
}
