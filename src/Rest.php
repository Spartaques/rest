<?php

namespace Polyloans\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Rest
{
    protected $client;
    protected $response;

    protected $method = 'GET';
    protected $headers = [];
    protected $uri = null;
    protected $body = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function response()
    {
        return $this->response;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function addHeader($key, $value)
    {
        if ($value === null) {
            unset($this->headers[$key]);
        } else {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    public function addHeaders(array $headers, $force = false)
    {
        if ($force) {
            $this->clearHeaders();
        }
        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }
        return $this;
    }

    public function clearHeaders()
    {
        $this->headers = [];
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getJSON($uri, array $headers = [])
    {
        $this->uri = $uri;
        $this->addHeader('Content-Type', 'application/json');
        $this->addHeader('Accept', 'application/json');
        $this->addHeaders($headers);
        return $this->execute('GET');
    }

    public function postXML($uri, $xml, array $headers = [])
    {
        $this->uri = $uri;
        $this->addHeader('Content-Type', 'application/xml');
        $this->addHeader('Accept', 'application/xml');
        $this->addHeaders($headers);
        $this->setBody($xml);
        return $this->execute('POST');
    }

    public function execute($method = null, $uri = null, $options = null)
    {
        try {
            return $this->response = $this->client->request(
                $method ? $method : $this->method,
                $uri ? $uri : $this->uri,
                array_merge(['headers' => $this->headers, 'body' => $this->body], (array) $options)
            );
        } catch (RequestException $e) {
            if ($e->getResponse()) {
                throw new RestException('HTTP ERROR', $e->getCode(), $e->getResponse()->getBody(true));
            } else {
                throw new RestException('CONNECTION ERROR: ' . $e->getMessage());
            }
        }
    }
}
