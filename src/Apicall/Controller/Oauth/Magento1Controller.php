<?php

namespace Ffm\Apicall\Controller\Oauth;

use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Server\Magento;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Magento1Controller
{
    /** @var \Cache\Adapter\Filesystem\FilesystemCachePool */
    protected $cache;
    /** @var array */
    protected $settings;

    /**
     * Use the slimphp dependencies for DI
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->cache = $container->get('cache');
        $this->settings = $container->get('settings')['magento1'];
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function request(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $server = $this->getServer();

        $temporaryCredentials = $server->getTemporaryCredentials();

        $cacheItem = $this->cache->getItem('oauth_token_secret');
        if ($cacheItem->isHit()) {
            $this->cache->deleteItem('oauth_token_secret');
        }
        $cacheItem->set(serialize($temporaryCredentials));
        $cacheItem->expiresAfter(new \DateInterval('P1D'));
        $this->cache->save($cacheItem);

        $server->authorize($temporaryCredentials);

        exit;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function callback(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $secretCache = $this->cache->getItem('oauth_token_secret');
        if (!$secretCache->isHit()) {
            $body = $response->getBody();
            $body->write('oauth secret not found');

            return $response->withStatus(404)
                ->withBody($body);
        }

        $server = $this->getServer();

        $temporaryCredentials = unserialize($secretCache->get(), [
            'allowed_class' => TemporaryCredentials::class
        ]);

        // Third and final part to OAuth 1.0 authentication is to retrieve token
        // credentials (formally known as access tokens in earlier OAuth 1.0
        // specs).
        $tokenCredentials = $server->getTokenCredentials(
            $temporaryCredentials,
            $request->getAttribute('oauth_token'),
            $request->getAttribute('oauth_verifier')
        );

        // Now, we'll store the token credentials and discard the temporary
        // ones - they're irrelevant at this stage.

        $this->cache->deleteItem('oauth_token_secret');

        $tokenCache = $this->cache->getItem('oauth_token');
        $tokenCache->set(serialize($tokenCredentials));
        $this->cache->save($tokenCache);

        $body = $response->getBody();
        $body->write('token and secret set');

        return $response->withStatus(200)
            ->withBody($body);
    }

    /**
     * @return Magento
     */
    protected function getServer(): Magento
    {
        return new Magento([
            'host' => $this->settings['host'],
            'identifier' => $this->settings['key'],
            'secret' => $this->settings['secret'],
            'callback_uri' => $this->settings['callback_url'],
            'admin' => true,
            'adminUrl' => $this->settings['admin']
        ]);
    }
}
