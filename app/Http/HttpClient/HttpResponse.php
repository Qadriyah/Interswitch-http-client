<?php

namespace App\Http\HttpClient;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;

class HttpResponse
{
    /**
     * HttpResponse constructor.
     * @param Response $response
     * @param $client
     */
    public function __construct(Response $response, $client)
    {
        $this->response = $response;
        $this->client = $client;
    }

    /**
     * Channel all dynamic method calls to the response
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->response->{$method}(...$arguments);
    }

    /**
     * Get response data
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function data($key = null, $default = null)
    {
        $data = json_decode($this->response->getBody()->getContents(), true);
        return $key ? Arr::get($data, $key, $default) : $data;
    }

    /**
     * Gets the client that was used to make the request
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }
}
