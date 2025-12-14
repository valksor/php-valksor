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

namespace Valksor\Component\FormType\HoneyPot\Form\Extension;

use InvalidArgumentException;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function error_log;
use function is_array;
use function sprintf;

class HoneyPotExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        if (!$options['honeypot']) {
            return;
        }

        // Add honeypot field
        $builder->add($options['honeypot_field_name'], TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'class' => 'hidden',
                'tabindex' => '-1',
                'autocomplete' => 'off',
            ],
        ]);

        // Add validation listener
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options): void {
            $data = $event->getData();

            if (is_array($data) && !empty($data[$options['honeypot_field_name']])) {
                // Bot detected - log and throw exception
                $request = $this->requestStack->getCurrentRequest();

                if ($request) {
                    // Log bot attempt
                    /* @noinspection ForgottenDebugOutputInspection */
                    error_log(sprintf(
                        'Bot detected on form submission - IP: %s, User-Agent: %s, Honeypot field: %s',
                        $request->getClientIp(),
                        $request->headers->get('User-Agent'),
                        $data[$options['honeypot_field_name']],
                    ));
                }

                throw new InvalidArgumentException($options['honeypot_message']);
            }
        });
    }

    public function configureOptions(
        OptionsResolver $resolver,
    ): void {
        $resolver->setDefaults([
            'honeypot' => false,
            'honeypot_field_name' => 'website',
            'honeypot_message' => 'This form should not be submitted by bots.',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
