<?php

namespace Tests\Unit\Httpbin;

use Ffm\Apicall\Httpbin\Exception;
use Ffm\Apicall\Httpbin\Get;
use GuzzleHttp\Client;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
{
    public function getMockLogger()
    {
        return $this->getMockBuilder(Logger::class)
            ->setConstructorArgs(['test'])
            ->getMock();
    }

    public function getMockClient($response)
    {
        $client = $this->getMockBuilder(Client::class)
            ->getMock();
        $client->method('request')
            ->willReturn($response);

        return $client;
    }

    public function testSuccesfulRequestApi()
    {
        $response = $this->getMockBuilder(\GuzzleHttp\Psr7\Response::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willReturn(200);
        $response->method('getBody')
            ->willReturn('{"args":{"customer":"200084726"}}');

        $api = new Get($this->getMockLogger(), $this->getMockClient($response));
        $response = $api->requestApi(10);

        $this->assertContains('"customer":"200084726"', json_encode($response));
    }

    public function testErrorRequestApi()
    {
        $response = $this->getMockBuilder(\GuzzleHttp\Psr7\Response::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willReturn(500);
        $response->method('getBody')
            ->willReturn('test invalid request');

        $api = new Get($this->getMockLogger(), $this->getMockClient($response));

        $caughtError = '';
        try {
            $response = $api->requestApi(10);
        } catch (Exception $error) {
            $caughtError = $error->getMessage();
        }

        $this->assertEquals('Could not request customer data', $caughtError);
    }

}
