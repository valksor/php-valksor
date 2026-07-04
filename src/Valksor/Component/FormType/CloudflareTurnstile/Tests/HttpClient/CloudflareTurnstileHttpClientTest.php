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

// PACKAGE: Verifies HTTP verification requests to Cloudflare API.

namespace Valksor\Component\FormType\CloudflareTurnstile\Tests\HttpClient;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Valksor\Component\FormType\CloudflareTurnstile\HttpClient\CloudflareTurnstileHttpClient;
use Valksor\Component\FormType\CloudflareTurnstile\Service\CloudflareTurnstileRegistry;

final class CloudflareTurnstileHttpClientTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private CloudflareTurnstileRegistry $registry;
    private CloudflareTurnstileHttpClient $turnstileHttpClient;

    public function testVerifyResponseReturnsFalseForInvalidType(): void
    {
        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Cloudflare Turnstile configuration "invalid" not found'));

        // An unknown type is rejected before any HTTP request is made.
        $this->httpClient->expects($this->never())
            ->method('request');

        $result = $this->turnstileHttpClient->verifyResponse('test-token', 'invalid');

        $this->assertFalse($result);
    }

    public function testVerifyResponseReturnsFalseOnFailure(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(['success' => false]);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);

        // A failed verification returns false without logging an error.
        $this->logger->expects($this->never())
            ->method('error');

        $result = $this->turnstileHttpClient->verifyResponse('invalid-token', 'default');

        $this->assertFalse($result);
    }

    public function testVerifyResponseReturnsFalseOnHttpException(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willThrowException($this->createStub(TransportExceptionInterface::class));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Cloudflare Turnstile HTTP exception'));

        $result = $this->turnstileHttpClient->verifyResponse('test-token', 'default');

        $this->assertFalse($result);
    }

    public function testVerifyResponseReturnsFalseWhenSuccessKeyMissing(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(['error' => 'something went wrong']);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);

        // A missing "success" key returns false without logging an error.
        $this->logger->expects($this->never())
            ->method('error');

        $result = $this->turnstileHttpClient->verifyResponse('test-token', 'default');

        $this->assertFalse($result);
    }

    public function testVerifyResponseReturnsTrueOnSuccess(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(['success' => true]);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'body' => [
                        'response' => 'test-token',
                        'secret' => 'secret_key_default',
                    ],
                ],
            )
            ->willReturn($response);

        // A successful verification does not log an error.
        $this->logger->expects($this->never())
            ->method('error');

        $result = $this->turnstileHttpClient->verifyResponse('test-token', 'default');

        $this->assertTrue($result);
    }

    public function testVerifyResponseUsesCorrectSecretForType(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(['success' => true]);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'body' => [
                        'response' => 'test-token',
                        'secret' => 'secret_key_contact',
                    ],
                ],
            )
            ->willReturn($response);

        // A successful verification does not log an error.
        $this->logger->expects($this->never())
            ->method('error');

        $result = $this->turnstileHttpClient->verifyResponse('test-token', 'contact');

        $this->assertTrue($result);
    }

    protected function setUp(): void
    {
        $parameterBag = new ParameterBag([
            'valksor.form_type.cloudflare_turnstile.types' => [
                'default' => [
                    'site_key' => 'site_key_default',
                    'secret_key' => 'secret_key_default',
                ],
                'contact' => [
                    'site_key' => 'site_key_contact',
                    'secret_key' => 'secret_key_contact',
                ],
            ],
        ]);

        $this->registry = new CloudflareTurnstileRegistry($parameterBag);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->turnstileHttpClient = new CloudflareTurnstileHttpClient(
            $this->registry,
            $this->httpClient,
            $this->logger,
        );
    }
}
