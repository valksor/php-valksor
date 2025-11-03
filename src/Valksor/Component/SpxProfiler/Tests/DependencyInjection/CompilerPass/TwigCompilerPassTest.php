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

namespace Valksor\Component\SpxProfiler\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Valksor\Component\SpxProfiler\DependencyInjection\CompilerPass\TwigCompilerPass;

final class TwigCompilerPassTest extends TestCase
{
    private TwigCompilerPass $compilerPass;
    private ContainerBuilder $container;

    public function testCompilerPassImplementsCompilerPassInterface(): void
    {
        $reflection = new ReflectionClass($this->compilerPass);
        $interfaces = $reflection->getInterfaceNames();

        $this->assertContains(
            'Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface',
            $interfaces,
            'Compiler pass should implement CompilerPassInterface',
        );
    }

    public function testCompilerPassIsInstantiable(): void
    {
        $this->assertInstanceOf(TwigCompilerPass::class, $this->compilerPass);
    }

    public function testProcessWhenTwigLoaderDoesNotExist(): void
    {
        // Don't add the twig loader definition
        $this->compilerPass->process($this->container);

        // Container should not have any issues
        $this->assertTrue(true, 'Process should complete without errors when twig loader does not exist');
    }

    public function testProcessWhenTwigLoaderExists(): void
    {
        // Add the twig loader definition
        $loaderDefinition = new Definition('dummy');
        $this->container->setDefinition('twig.loader.native_filesystem', $loaderDefinition);

        // Process the compiler pass
        $this->compilerPass->process($this->container);

        // Check that addPath method was called
        $methodCalls = $loaderDefinition->getMethodCalls();
        $this->assertNotEmpty($methodCalls, 'Loader should have method calls');

        $addPathCall = null;

        foreach ($methodCalls as $call) {
            if ('addPath' === $call[0]) {
                $addPathCall = $call;

                break;
            }
        }

        $this->assertNotNull($addPathCall, 'addPath method should be called');
        $this->assertCount(2, $addPathCall[1], 'addPath should have 2 arguments');

        [$path, $namespace] = $addPathCall[1];
        $this->assertStringContainsString('Resources/views', $path, 'Path should contain Resources/views');
        $this->assertSame('ValksorSpx', $namespace, 'Namespace should be ValksorSpx');
    }

    protected function setUp(): void
    {
        $this->compilerPass = new TwigCompilerPass();
        $this->container = new ContainerBuilder();
    }
}
