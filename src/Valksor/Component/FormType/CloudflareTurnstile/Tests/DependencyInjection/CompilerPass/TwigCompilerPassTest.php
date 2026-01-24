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

// PACKAGE: Verifies Twig path and form resources registration.

namespace Valksor\Component\FormType\CloudflareTurnstile\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Valksor\Component\FormType\CloudflareTurnstile\DependencyInjection\CompilerPass\TwigCompilerPass;

final class TwigCompilerPassTest extends TestCase
{
    private TwigCompilerPass $compilerPass;

    public function testProcessAddsTwigPathAndFormResources(): void
    {
        $definition = $this->createMock(Definition::class);
        $definition->expects($this->once())
            ->method('addMethodCall')
            ->with(
                'addPath',
                $this->callback(static fn (array $args) => str_contains($args[0], 'Resources/views')
                        && 'ValksorFTCloudflareTurnstile' === $args[1]),
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
                $this->callback(static fn (array $resources) => '@ValksorFTCloudflareTurnstile/fields.html.twig' === $resources[0]
                        && '@SomeBundle/form.html.twig' === $resources[1]),
            );

        $this->compilerPass->process($container);
    }

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
