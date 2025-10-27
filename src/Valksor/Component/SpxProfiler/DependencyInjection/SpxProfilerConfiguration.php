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

namespace Valksor\Component\SpxProfiler\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Valksor\Bundle\DependencyInjection\AbstractDependencyConfiguration;
use Valksor\Component\SpxProfiler\DependencyInjection\CompilerPass\TwigCompilerPass;

class SpxProfilerConfiguration extends AbstractDependencyConfiguration
{
    public function build(
        ContainerBuilder $container,
    ): void {
        $container->addCompilerPass(new TwigCompilerPass());
    }
}
