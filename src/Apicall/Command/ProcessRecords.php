<?php

namespace Ffm\Apicall\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessRecords extends Command
{
    /** @var \Monolog\Logger */
    protected $log;
    /** @var \Cache\Adapter\Filesystem\FilesystemCachePool */
    protected $cache;
    /** @var \GuzzleHttp\ClientInterface */
    protected $client;

    /**
     * Use the slimphp dependencies for DI
     * @param string $name
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(string $name, ContainerInterface $container)
    {
        parent::__construct($name);

        $this->log = $container->get('logger');
        $this->cache = $container->get('cache');
        $this->client = $container->get('httpclient');
    }

    /**
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('records:process')
            ->setDescription('Enrich known records with data')
            ->setHelp('Use the existing given records and enrich them with data')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);
        $resourceUrl = "$apiUrl/products";
        $oauthClient->fetch($resourceUrl, array(), 'GET', array('Content-Type' => 'application/json', 'Accept' => '*/*'));
        $productsList = json_decode($oauthClient->getLastResponse());
        print_r($productsList);

        $output->write('Hello world');
    }
}