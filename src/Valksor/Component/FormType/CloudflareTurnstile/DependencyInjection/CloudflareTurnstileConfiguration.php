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

namespace Valksor\Component\FormType\CloudflareTurnstile\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Valksor\Bundle\DependencyInjection\AbstractDependencyConfiguration;
use Valksor\Bundle\ValksorBundle;
use Valksor\Component\FormType\CloudflareTurnstile\DependencyInjection\CompilerPass\TwigCompilerPass;

use function sprintf;

class CloudflareTurnstileConfiguration extends AbstractDependencyConfiguration
{
    public function addSection(
        ArrayNodeDefinition $rootNode,
        callable $enableIfStandalone,
        string $component,
    ): void {
        $rootNode
            ->children()
                ->arrayNode($component)
                    ->{$enableIfStandalone(sprintf('%s/%s', ValksorBundle::VALKSOR, $component), self::class)}()
                    ->children()
                        ->arrayNode('types')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('site_key')
                                        ->info('Cloudflare Turnstile widget site key')
                                    ->end()
                                    ->scalarNode('secret_key')
                                        ->info('Cloudflare Turnstile widget secret key')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function build(
        ContainerBuilder $container,
    ): void {
        $container->addCompilerPass(new TwigCompilerPass());
    }

    public function usesArrayPrototype(): bool
    {
        return true;
    }
}
