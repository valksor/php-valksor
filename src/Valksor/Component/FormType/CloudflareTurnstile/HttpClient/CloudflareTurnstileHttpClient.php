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

namespace Valksor\Component\FormType\CloudflareTurnstile\HttpClient;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Valksor\Component\FormType\CloudflareTurnstile\Service\CloudflareTurnstileRegistry;

use function array_key_exists;
use function sprintf;

final class CloudflareTurnstileHttpClient
{
    private const string SITEVERIFY_ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        private readonly CloudflareTurnstileRegistry $registry,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Verify Turnstile response using the specified configuration type.
     */
    public function verifyResponse(
        string $turnstileResponse,
        string $type,
    ): bool {
        try {
            $secret = $this->registry->getSecretKey($type);
        } catch (InvalidArgumentException $e) {
            $this->logger->error(
                sprintf(
                    'Cloudflare Turnstile configuration "%s" not found: %s',
                    $type,
                    $e->getMessage(),
                ),
            );

            return false;
        }

        $response = $this->httpClient->request(
            Request::METHOD_POST,
            self::SITEVERIFY_ENDPOINT,
            [
                'body' => [
                    'response' => $turnstileResponse,
                    'secret' => $secret,
                ],
            ],
        );

        try {
            $content = $response->toArray();
        } catch (ExceptionInterface $e) {
            $this->logger->error(
                sprintf(
                    'Cloudflare Turnstile HTTP exception (%s) with a message: %s',
                    $e::class,
                    $e->getMessage(),
                ),
            );

            return false;
        }

        return array_key_exists('success', $content) && true === $content['success'];
    }
}
