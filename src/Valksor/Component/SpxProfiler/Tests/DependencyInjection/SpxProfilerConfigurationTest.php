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

namespace Valksor\Component\SpxProfiler\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Valksor\Component\SpxProfiler\DependencyInjection\SpxProfilerConfiguration;

final class SpxProfilerConfigurationTest extends TestCase
{
    private SpxProfilerConfiguration $configuration;
    private ContainerBuilder $container;

    public function testConfigurationBuild(): void
    {
        $this->configuration->build($this->container);

        // Check that compiler passes are registered
        $passes = $this->container->getCompilerPassConfig()->getPasses();

        $hasTwigCompilerPass = false;

        foreach ($passes as $pass) {
            $reflection = new ReflectionClass($pass);

            if ('TwigCompilerPass' === $reflection->getShortName()) {
                $hasTwigCompilerPass = true;

                break;
            }
        }

        $this->assertTrue($hasTwigCompilerPass, 'TwigCompilerPass should be registered');
    }

    public function testConfigurationIsSingleton(): void
    {
        $configuration1 = new SpxProfilerConfiguration();
        $configuration2 = new SpxProfilerConfiguration();

        $this->assertNotSame($configuration1, $configuration2, 'Configuration should not be singleton');
    }

    protected function setUp(): void
    {
        $this->configuration = new SpxProfilerConfiguration();
        $this->container = new ContainerBuilder();
    }
}
