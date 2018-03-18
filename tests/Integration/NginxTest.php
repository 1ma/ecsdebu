<?php

declare(strict_types=1);

namespace Project\Tests\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;

class NginxTest extends TestCase
{
    /**
     * @var Client
     */
    private $http;

    protected function setUp()
    {
        $this->http = new Client([
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::DEBUG => (bool) $_ENV['HTTP_DEBUG']
        ]);
    }

    /**
     * The response to an HTTP request that is forwarded to the FPM container
     * will have an X-Powered-By header with the PHP version, and Nginx will
     * not put an ETag on it.
     */
    public function testDynamicRequestIsForwardedToFpm(): void
    {
        $response = $this->http->get('http://project.devel/phpinfo');

        self::assertSame(200, $response->getStatusCode());
        self::assertFalse($response->hasHeader('ETag'));
        self::assertTrue($response->hasHeader('X-Powered-By'));
    }

    /**
     * The response to an HTTP request for a static file will have an ETag
     * header with its hash, but not an X-Powered-By header since the FPM
     * container was not involved at all.
     */
    public function testStaticFileRequestIsHandledByNginx(): void
    {
        $response = $this->http->get('http://project.devel/favicon.png');

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($response->hasHeader('ETag'));
        self::assertFalse($response->hasHeader('X-Powered-By'));
    }

    /**
     * The front controller (index.php) must be invisible to the outside
     * world, hence an HTTP request for it must return a 404 response.
     */
    public function testFrontControllerFileIsInvisible(): void
    {
        $response = $this->http->get('http://project.devel/index.php');

        self::assertSame(404, $response->getStatusCode());
    }
}
