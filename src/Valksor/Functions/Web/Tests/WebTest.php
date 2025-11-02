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

namespace Valksor\Functions\Web\Traits;

if (!\function_exists(__NAMESPACE__ . '\\file_get_contents')) {
    function file_get_contents(
        string $filename,
        bool $use_include_path = false,
        $context = null,
        int $offset = 0,
        ?int $length = null,
    ): string|false {
        if ('https://api.ipify.org/' === $filename) {
            return '198.51.100.42';
        }

        if (str_starts_with($filename, 'https://api.github.com/repos/')) {
            return '{"tag_name":"v2.0.0"}';
        }

        return file_get_contents($filename, $use_include_path, $context, $offset, $length);
    }
}

namespace Valksor\Functions\Web\Tests;

use CURLFile;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Valksor\Functions\Web\Functions;

final class WebTest extends TestCase
{
    private Functions $web;

    public function testArrayFromQueryStringParsesKeys(): void
    {
        $result = $this->web->arrayFromQueryString('foo%5Bbar%5D=baz&encoded=sp+ace');

        self::assertSame([
            'foo[bar]' => 'baz',
            'encoded' => 'sp ace',
        ], $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testBuildArrayFromObjectProducesArray(): void
    {
        $object = new class {
            public string $name = 'john';
            public array $meta = ['role' => 'admin'];
        };

        self::assertSame(
            ['name' => 'john', 'meta' => ['role' => 'admin']],
            $this->web->buildArrayFromObject($object),
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testBuildHttpQueryArrayHandlesObjectsArraysAndFiles(): void
    {
        $file = new CURLFile(__FILE__);

        $object = new class($file) {
            public string $name = 'john';
            public array $meta = ['roles' => ['admin']];

            public function __construct(
                public CURLFile $attachment,
            ) {
            }
        };

        $result = $this->web->buildHttpQueryArray($object);

        self::assertSame('john', $result['name']);
        self::assertSame('admin', $result['meta[roles][0]']);
        self::assertSame($file, $result['attachment']);
    }

    /**
     * @throws ReflectionException
     */
    public function testBuildHttpQueryStringBuildsQuery(): void
    {
        $payload = new class {
            public string $name = 'john';
            public array $meta = ['role' => 'admin'];
        };

        self::assertSame('name=john&meta%5Brole%5D=admin', $this->web->buildHttpQueryString($payload));
    }

    public function testCIDRRangeAndValidation(): void
    {
        self::assertSame(['0', '0'], $this->web->CIDRRange('invalid'));

        $range = $this->web->CIDRRange('192.168.0.0/24');
        self::assertSame(['3232235520', '3232235775'], $range);

        $rangeHuman = $this->web->CIDRRange('192.168.0.0/24', false);
        self::assertSame(['192.168.0.0', '192.168.0.255'], $rangeHuman);

        self::assertTrue($this->web->validateCIDR('192.168.0.0/24'));
        self::assertFalse($this->web->validateCIDR('192.168.0.1/33'));
    }

    public function testCheckForwardedHeaders(): void
    {
        self::assertTrue($this->web->checkHttpXForwardedSsl($this->createRequest([Functions::HEADER_SSL => 'on'])));
        self::assertTrue($this->web->checkHttpXForwardedProto($this->createRequest([Functions::HEADER_PROTO => 'https'])));
        self::assertFalse($this->web->checkHttpXForwardedSsl($this->createRequest()));
    }

    public function testCheckHttps(): void
    {
        self::assertTrue($this->web->checkHttps($this->createRequest([Functions::HEADER_HTTPS => 'on'])));
        self::assertFalse($this->web->checkHttps($this->createRequest()));
    }

    public function testCheckServerPort(): void
    {
        self::assertTrue($this->web->checkServerPort($this->createRequest([Functions::HEADER_PORT => Functions::HTTPS])));
        self::assertFalse($this->web->checkServerPort($this->createRequest([Functions::HEADER_PORT => Functions::HTTP])));
    }

    public function testIsAbsolute(): void
    {
        self::assertTrue($this->web->isAbsolute('https://example.com'));
        self::assertTrue($this->web->isAbsolute('//cdn.example.com')); // schema-relative
        self::assertFalse($this->web->isAbsolute('/relative/path'));
    }

    public function testIsCIDR(): void
    {
        self::assertTrue($this->web->isCIDR('10.0.0.0/8'));
        self::assertFalse($this->web->isCIDR('10.0.0.0/40'));
    }

    public function testIsHttpsCoversAllStrategies(): void
    {
        self::assertTrue($this->web->isHttps($this->createRequest([Functions::HEADER_HTTPS => 'on'])));
        self::assertTrue($this->web->isHttps($this->createRequest([Functions::HEADER_PORT => (string) Functions::HTTPS])));
        self::assertTrue($this->web->isHttps($this->createRequest([Functions::HEADER_SSL => 'on'])));
        self::assertTrue($this->web->isHttps($this->createRequest([Functions::HEADER_PROTO => 'https'])));
        self::assertFalse($this->web->isHttps($this->createRequest()));
    }

    public function testIsIE(): void
    {
        $ieRequest = $this->createRequest(['HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; MSIE 10.0; Trident/7.0)']);
        $chromeRequest = $this->createRequest(['HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/122.0']);

        self::assertTrue($this->web->isIE($ieRequest));
        self::assertFalse($this->web->isIE($chromeRequest));
    }

    public function testIsUrl(): void
    {
        self::assertTrue($this->web->isUrl('https://example.com/path?foo=bar'));
        self::assertFalse($this->web->isUrl('not a url'));
    }

    public function testLatestReleaseTagUsesGithubApi(): void
    {
        self::assertSame('v2.0.0', $this->web->latestReleaseTag('valksor/php-functions-web'));
    }

    public function testParseHeadersAndRawHeaders(): void
    {
        $raw = 'HTTP/1.1 200 OK: \\r\\nContent-Type: text/plain\\r\\nX-Test: value';
        self::assertSame(
            [
                'status' => 'HTTP/1.1 200 OK',
                'Content-Type' => 'text/plain',
                'X-Test' => 'value',
            ],
            $this->web->parseHeaders($raw),
        );

        $headerString = $this->web->rawHeaders(new HeaderBag([
            'Content-Type' => ['text/plain'],
            'X-Test' => ['value'],
        ]));

        self::assertSame('content-type: text/plain\\r\\nx-test: value\\r\\n', $headerString);
    }

    public function testRemoteIpCFPrefersConnectingHeader(): void
    {
        $cfRequest = $this->createRequest([Functions::HTTP_CF_CONNECTING_IP => '203.0.113.77']);
        self::assertSame('203.0.113.77', $this->web->remoteIpCF($cfRequest));

        $fallback = $this->createRequest([Functions::REMOTE_ADDR => '10.0.0.1']);
        self::assertSame('10.0.0.1', $this->web->remoteIpCF($fallback));
    }

    public function testRemoteIpColumns(): void
    {
        $request = $this->createRequest([Functions::REMOTE_ADDR => '10.0.0.1']);
        self::assertSame('10.0.0.1', $this->web->remoteIp($request));

        $trusted = $this->createRequest([
            Functions::HTTP_CLIENT_IP => '198.51.100.10',
            Functions::HTTP_X_REAL_IP => '192.0.2.5',
            Functions::HTTP_X_FORWARDED_FOR => '203.0.113.11',
            Functions::REMOTE_ADDR => '10.0.0.1',
        ]);

        self::assertSame('198.51.100.10', $this->web->remoteIp($trusted, true));
    }

    /**
     * @throws ReflectionException
     */
    public function testRequestIdentityBuildsAugmentedData(): void
    {
        $request = Request::create('/path', 'POST', ['form' => 'value'], server: ['REQUEST_TIME' => '123']);
        $identity = $this->web->requestIdentity($request);

        self::assertSame('value', $identity['request']['parameters']['form']);
        self::assertMatchesRegularExpression('/^\d{1,3}(?:\.\d{1,3}){3}$/', $identity['actualIp']);
        self::assertStringStartsWith('123', $identity['uuid']);
        self::assertSame(35, \strlen($identity['uuid']));
    }

    public function testRequestMethodsReturnsListFromRequestClass(): void
    {
        $expected = [
            'HEAD',
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'PURGE',
            'OPTIONS',
            'TRACE',
            'CONNECT',
            'QUERY',
        ];

        self::assertSame($expected, $this->web->requestMethods());
    }

    /**
     * @throws ReflectionException
     */
    public function testResultRecursivelyExpandsStructures(): void
    {
        $values = $this->web->buildHttpQueryArray([
            'plain' => 1,
            'nested' => ['a' => 'b'],
        ]);

        self::assertSame(
            [
                'plain' => 1,
                'nested[a]' => 'b',
            ],
            $values,
        );
    }

    public function testRouteExistsChecksCollection(): void
    {
        $collection = new RouteCollection();
        $collection->add('existing', new Route('/path'));

        $router = new readonly class($collection) implements RouterInterface {
            public function __construct(
                private RouteCollection $collection,
            ) {
            }

            public function setContext(
                RequestContext $context,
            ): void {
            }

            public function getContext(): RequestContext
            {
                return new RequestContext();
            }

            public function generate(
                string $name,
                array $parameters = [],
                int $referenceType = self::ABSOLUTE_PATH,
            ): string {
                throw new RuntimeException('unused');
            }

            public function match(
                string $pathinfo,
            ): array {
                throw new RuntimeException('unused');
            }

            public function matchRequest(
                Request $request,
            ): array {
                throw new RuntimeException('unused');
            }

            public function getRouteCollection(): RouteCollection
            {
                return $this->collection;
            }

            public function getMatcher(): UrlMatcherInterface
            {
                throw new RuntimeException('unused');
            }

            public function getGenerator(): UrlGeneratorInterface
            {
                throw new RuntimeException('unused');
            }
        };

        self::assertTrue($this->web->routeExists($router, 'existing'));
        self::assertFalse($this->web->routeExists($router, 'missing'));
    }

    public function testSchemaUsesHttpsDetection(): void
    {
        self::assertSame(Functions::SCHEMA_HTTP, $this->web->schema($this->createRequest()));
        self::assertSame(Functions::SCHEMA_HTTPS, $this->web->schema($this->createRequest([Functions::HEADER_PROTO => 'https'])));
    }

    public function testUrlEncodeNormalisesPortAndQuery(): void
    {
        $url = 'https://example.com:8080/path?foo=bar+baz&arr%5B0%5D=1';
        self::assertSame($url, $this->web->urlEncode($url));
    }

    public function testValidateEmailAndIp(): void
    {
        self::assertTrue($this->web->validateEmail('user@example.com'));
        self::assertFalse($this->web->validateEmail('invalid@'));

        self::assertTrue($this->web->validateIPAddress('203.0.113.1'));
        self::assertFalse($this->web->validateIPAddress('10.0.0.1'));
        self::assertTrue($this->web->validateIPAddress('10.0.0.1', deny: false));
    }

    protected function setUp(): void
    {
        $this->web = new Functions();
    }

    private function createRequest(
        array $server = [],
    ): Request {
        return new Request(server: $server);
    }
}
