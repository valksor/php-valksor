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

namespace Valksor\Component\FormType\CloudflareTurnstile\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigCompilerPass implements CompilerPassInterface
{
    public function process(
        ContainerBuilder $container,
    ): void {
        if (!$container->hasParameter('twig.form.resources') || !$container->hasDefinition('twig.loader.native_filesystem')) {
            return;
        }

        $definition = $container->getDefinition('twig.loader.native_filesystem');
        $definition->addMethodCall('addPath', [
            __DIR__ . '/../../Resources/views',
            'ValksorFTCloudflareTurnstile',
        ]);

        $resources = $container->getParameter('twig.form.resources') ?: [];
        array_unshift($resources, '@ValksorFTCloudflareTurnstile/fields.html.twig');
        $container->setParameter('twig.form.resources', $resources);
    }
}
