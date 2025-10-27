<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Dāvis Zālītis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Functions\Web;

final class Functions
{
    use Traits\_ArrayFromQueryString;
    use Traits\_BuildArrayFromObject;
    use Traits\_BuildHttpQueryArray;
    use Traits\_BuildHttpQueryString;
    use Traits\_CheckHttps;
    use Traits\_CheckHttpXForwardedProto;
    use Traits\_CheckHttpXForwardedSsl;
    use Traits\_CheckServerPort;
    use Traits\_CIDRRange;
    use Traits\_IsAbsolute;
    use Traits\_IsCIDR;
    use Traits\_IsHttps;
    use Traits\_IsIE;
    use Traits\_IsUrl;
    use Traits\_LatestReleaseTag;
    use Traits\_ParseHeaders;
    use Traits\_RawHeaders;
    use Traits\_RemoteIp;
    use Traits\_RemoteIpCF;
    use Traits\_RequestIdentity;
    use Traits\_RequestMethods;
    use Traits\_Result;
    use Traits\_RouteExists;
    use Traits\_Schema;
    use Traits\_UrlEncode;
    use Traits\_ValidateCIDR;
    use Traits\_ValidateEmail;
    use Traits\_ValidateIPAddress;

    public const string HEADER_HTTPS = 'HTTPS';
    public const string HEADER_PORT = 'SERVER_PORT';
    public const string HEADER_PROTO = 'HTTP_X_FORWARDED_PROTO';
    public const string HEADER_SSL = 'HTTP_X_FORWARDED_SSL';
    public const string HTML = 'text/html';
    public const int HTTP = 80;
    public const int HTTPS = 443;
    public const int HTTP_BANDWIDTH_LIMIT_EXCEEDED = 509;
    public const int HTTP_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
    public const string HTTP_CF_CONNECTING_IP = 'HTTP_CF_CONNECTING_IP';
    public const string HTTP_CLIENT_IP = 'HTTP_CLIENT_IP';
    public const int HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE = 444;
    public const int HTTP_DISCONNECTED_OPERATION = 112;
    public const int HTTP_ENHANCE_YOUR_CALM = 420;
    public const int HTTP_HEURISTIC_EXPIRATION = 113;
    public const int HTTP_INVALID_TOKEN = 498;
    public const int HTTP_MISCELLANEOUS_PERSISTENT_WARNING = 299;
    public const int HTTP_MISCELLANEOUS_WARNING = 199;
    public const int HTTP_NETWORK_CONNECT_TIMEOUT_ERROR = 599;
    public const int HTTP_NETWORK_READ_TIMEOUT_ERROR = 598;
    public const int HTTP_RESPONSE_IS_STALE = 110;
    public const int HTTP_RETRY_WITH = 449;
    public const int HTTP_REVALIDATION_FAILED = 111;
    public const int HTTP_SITE_IS_FROZEN = 530;
    public const int HTTP_SITE_IS_OVERLOADED = 529;
    public const int HTTP_THIS_IS_FINE = 218;
    public const int HTTP_TOKEN_REQUIRED = 499;
    public const int HTTP_TRANSFORMATION_APPLIED = 214;
    public const string HTTP_X_FORWARDED_FOR = 'HTTP_X_FORWARDED_FOR';
    public const string HTTP_X_REAL_IP = 'HTTP_X_REAL_IP';
    public const string JSON = 'application/json';
    public const string JSONLD = 'application/ld+json';
    public const string JSON_PATCH = 'application/merge-patch+json';
    public const string REMOTE_ADDR = 'REMOTE_ADDR';
    public const string SCHEMA_HTTP = 'http://';
    public const string SCHEMA_HTTPS = 'https://';
    public const string XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    public const string XML = 'application/xml';
    public const string X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
}
