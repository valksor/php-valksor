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

namespace Valksor\Component\FormType\CloudflareTurnstile\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Valksor\Component\FormType\CloudflareTurnstile\Constraints\CloudflareTurnstile;
use Valksor\Component\FormType\CloudflareTurnstile\Service\CloudflareTurnstileRegistry;

use function sprintf;

class CloudflareTurnstileType extends AbstractType
{
    public function __construct(
        private readonly CloudflareTurnstileRegistry $registry,
    ) {
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options,
    ): void {
        $view->vars['key'] = $this->registry->getSiteKey($options['type']);
        // Pass the type to the view for potential use in templates
        $view->vars['turnstile_type'] = $options['type'];
    }

    public function configureOptions(
        OptionsResolver $resolver,
    ): void {
        $resolver
            ->setDefaults([
                'mapped' => false,
                'type' => null, // Will be required through validation
                'constraints' => static fn (array $options): CloudflareTurnstile => new CloudflareTurnstile(
                    type: $options['type'],
                ),
            ])
            ->setAllowedTypes('type', ['string', 'null'])
            ->setNormalizer('type', function (OptionsResolver $resolver, ?string $value): string {
                if (null === $value) {
                    throw new InvalidOptionsException('The "type" option is required for CloudflareTurnstileType. Available types: ' . implode(', ', $this->registry->getAvailableTypes()));
                }

                if (!$this->registry->hasType($value)) {
                    throw new InvalidOptionsException(sprintf('Invalid Cloudflare Turnstile type "%s". Available types: %s', $value, implode(', ', $this->registry->getAvailableTypes())));
                }

                return $value;
            });
    }

    public function getBlockPrefix(): string
    {
        return 'valksor_form_type_cloudflare_turnstile';
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
