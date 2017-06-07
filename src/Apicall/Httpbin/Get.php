<?php

namespace Ffm\Apicall\Httpbin;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Psr7;

class Get
{
    /** @var \Monolog\Logger */
    protected $log;
    /** @var \GuzzleHttp\Client */
    protected $client;

    /**
     * Get constructor.
     * @param LoggerInterface $log
     */
    public function __construct(LoggerInterface $log, ClientInterface $client)
    {
        $this->log = $log;
        $this->client = $client;
    }

    public function requestApi(int $reference): array
    {
        try {
            $response = $this->client->request('GET', 'https://httpbin.org/get', [
                'query' => ['customer' => $reference]
            ]);
        } catch (RequestException $e) {
            $this->log->addError('request error request: ' . Psr7\str($e->getRequest()));

            if ($e->hasResponse()) {
                $this->log->addError('request error response: ' . Psr7\str($e->getResponse()));
            }

            throw new Exception("error requesting data");
        }

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Could not request customer data");
        }

        return \GuzzleHttp\json_decode($response->getBody(), true);
    }
}