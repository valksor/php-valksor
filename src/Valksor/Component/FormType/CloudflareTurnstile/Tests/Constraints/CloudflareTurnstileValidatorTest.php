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

// PACKAGE: Verifies turnstile response validation logic.

namespace Valksor\Component\FormType\CloudflareTurnstile\Tests\Constraints;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Valksor\Component\FormType\CloudflareTurnstile\Constraints\CloudflareTurnstile;
use Valksor\Component\FormType\CloudflareTurnstile\Constraints\CloudflareTurnstileValidator;
use Valksor\Component\FormType\CloudflareTurnstile\HttpClient\CloudflareTurnstileHttpClient;
use Valksor\Component\FormType\CloudflareTurnstile\Service\CloudflareTurnstileRegistry;

final class CloudflareTurnstileValidatorTest extends TestCase
{
    private ExecutionContextInterface $context;
    private RequestStack $requestStack;
    private HttpClientInterface $symfonyHttpClient;
    private CloudflareTurnstileValidator $validator;

    public function testValidateAddsViolationWhenHttpClientReturnsFalse(): void
    {
        $constraint = new CloudflareTurnstile(type: 'default');

        $request = new Request([], ['cf-turnstile-response' => 'test-token']);
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(['success' => false]);

        $this->symfonyHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder);

        $this->validator->validate('value', $constraint);
    }

    public function testValidateAddsViolationWhenNoRequest(): void
    {
        $constraint = new CloudflareTurnstile(type: 'default');

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder);

        $this->validator->validate('value', $constraint);
    }

    public function testValidateAddsViolationWhenTurnstileResponseIsEmpty(): void
    {
        $constraint = new CloudflareTurnstile(type: 'default');

        $request = new Request();
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder);

        $this->validator->validate('value', $constraint);
    }

    public function testValidateAddsViolationWhenTypeIsNull(): void
    {
        $constraint = new CloudflareTurnstile(type: null);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ type }}', 'undefined')
            ->willReturnSelf();
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->notFoundMessage)
            ->willReturn($violationBuilder);

        $this->validator->validate('value', $constraint);
    }

    public function testValidatePassesWhenHttpClientReturnsTrue(): void
    {
        $constraint = new CloudflareTurnstile(type: 'default');

        $request = new Request([], ['cf-turnstile-response' => 'valid-token']);
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(['success' => true]);

        $this->symfonyHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('value', $constraint);
    }

    public function testValidateThrowsExceptionForWrongConstraintType(): void
    {
        $wrongConstraint = $this->createMock(Constraint::class);

        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('value', $wrongConstraint);
    }

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->symfonyHttpClient = $this->createMock(HttpClientInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $parameterBag = new ParameterBag([
            'valksor.form_type.cloudflare_turnstile.types' => [
                'default' => [
                    'site_key' => 'site_key_default',
                    'secret_key' => 'secret_key_default',
                ],
            ],
        ]);

        $registry = new CloudflareTurnstileRegistry($parameterBag);
        $httpClient = new CloudflareTurnstileHttpClient(
            $registry,
            $this->symfonyHttpClient,
            new NullLogger(),
        );

        $this->validator = new CloudflareTurnstileValidator(
            $this->requestStack,
            $httpClient,
        );
        $this->validator->initialize($this->context);
    }
}
