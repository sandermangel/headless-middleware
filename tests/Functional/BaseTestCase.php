<?php

namespace Tests\Functional;

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Client;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends TestCase
{
    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @return \Slim\Http\Response
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function runApp($requestMethod, $requestUri, $requestData = null)
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Set up a response object
        $response = new Response();

        // Use the application settings
        $settings = require __DIR__ . '/../../src/settings.php';

        // Instantiate the application
        $app = new App($settings);

        $container = $app->getContainer();


        $container['logger'] = function ($container) {

            return $this->getMockBuilder(Logger::class)
                ->setConstructorArgs(['test'])
                ->getMock();
        };

        $container['cache'] = function ($container) {
            return new ArrayCachePool();
        };

        $container['httpclient'] = function ($container) {

            $response = $this->getMockBuilder(\GuzzleHttp\Psr7\Response::class)
                ->getMock();
            $response->method('getStatusCode')
                ->willReturn(200);
            $response->method('getBody')
                ->willReturn('{"args":{"customer":"200084726"},"headers":{"Connection":"close","Host":"httpbin.org","User-Agent":"GuzzleHttp\/6.2.1 curl\/7.47.0 PHP\/7.0.15-0ubuntu0.16.04.4"},"origin":"77.173.111.153","url":"https:\/\/httpbin.org\/get?customer=200084726"}');

            $client = $this->getMockBuilder(Client::class)
                ->getMock();
            $client->method('request')
                ->willReturn($response);

            return $client;
        };

        // Register routes
        require __DIR__ . '/../../src/routes.php';

        // Process the application
        $response = $app->process($request, $response);

        // Return the response
        return $response;
    }
}
