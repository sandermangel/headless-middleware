<?php

namespace Ffm\Apicall\Controller;

use Ffm\Apicall\Model\Transaction;
use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetdataController
{
    /** @var \Monolog\Logger */
    protected $log;
    /** @var \Cache\Adapter\Filesystem\FilesystemCachePool */
    protected $cache;
    /** @var ClientInterface */
    protected $client;

    /**
     * Use the slimphp dependencies for DI
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->log = $container->get('logger');
        $this->cache = $container->get('cache');
        $this->client = $container->get('httpclient');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $postbody = $request->getBody();

        $line = strtok($postbody, PHP_EOL);
        $header = false;
        $transactions = [];

        while ($line !== false) {
            if (is_array($header)) {
                $row = array_combine($header, str_getcsv($line));

                preg_match('/([\d]{1,2})-([\d]{1,2})-([\d]{4,4})T([\d\:]{8,8})/', $row['date'], $date);

                $transactionDate = new \DateTime("{$date[3]}-{$date[2]}-{$date[1]} {$date[4]}");

                $transactions[] = [
                    'uid' => uniqid('', true),
                    'transaction_id' => $row['id'],
                    'transaction_date' => $transactionDate->format('Y-m-d H:i:s'),
                    'amount' => $row['amount'],
                    'description' => $row['description'],
                    'order_reference' => $row['order reference']
                ];
                unset($row);
            }

            if (!$header) {
                $header = str_getcsv($line);
            }

            $line = strtok(PHP_EOL);
        }

        Transaction::insertOnDuplicateKey($transactions);

        return $response->withJson(['success' => 'OK'], 200);
    }
}