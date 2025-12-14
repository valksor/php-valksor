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

namespace Valksor\Component\FormType\CloudflareTurnstile\Constraints;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Valksor\Component\FormType\CloudflareTurnstile\HttpClient\CloudflareTurnstileHttpClient;

final class CloudflareTurnstileValidator extends ConstraintValidator
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly CloudflareTurnstileHttpClient $turnstileHttpClient,
    ) {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate(
        mixed $value,
        Constraint $constraint,
    ): void {
        if (!$constraint instanceof CloudflareTurnstile) {
            throw new UnexpectedTypeException($constraint, CloudflareTurnstile::class);
        }

        if (null === $constraint->type) {
            $this->context->buildViolation($constraint->notFoundMessage)
                ->setParameter('{{ type }}', 'undefined')
                ->addViolation();

            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        $turnstileResponse = (string) $request->request->get('cf-turnstile-response');

        if ('' === $turnstileResponse) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        if (!$this->turnstileHttpClient->verifyResponse($turnstileResponse, $constraint->type)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
