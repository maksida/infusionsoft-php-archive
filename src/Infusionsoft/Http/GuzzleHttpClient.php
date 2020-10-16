<?php

namespace Infusionsoft\Http;

use fXmlRpc\Transport\HttpAdapterTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class GuzzleHttpClient extends Client implements ClientInterface
{

    public $debug;
    public $httpLogAdapter;

    public function __construct($debug, LoggerInterface $httpLogAdapter)
    {
        $this->debug = $debug;
        $this->httpLogAdapter = $httpLogAdapter;

        $config = ['timeout' => 60];
        if ($this->debug) {
            $config['handler'] = HandlerStack::create();
            $config['handler']->push(
                Middleware::log($this->httpLogAdapter, new MessageFormatter(MessageFormatter::DEBUG))
            );
        }

        parent::__construct($config);
    }

    /**
     * @return \fXmlRpc\Transport\TransportInterface
     */
    public function getXmlRpcTransport()
    {

        $adapter = new Client($this);

        return new HttpAdapterTransport(new \Http\Message\MessageFactory\DiactorosMessageFactory(), $adapter);
    }

    /**
     * Sends a request to the given URI and returns the raw response.
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Infusionsoft\Http\HttpException
     */
    public function request($method, $uri = '', array $options = []): ResponseInterface
    {
        if (!isset($options['headers'])) {
            $options['headers'] = [];
        }

        if (!isset($options['body'])) {
            $options['body'] = null;
        }

        try {
            $options[RequestOptions::SYNCHRONOUS] = true;
            return $this->requestAsync($method, $uri, $options)->wait();
        } catch (BadResponseException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
