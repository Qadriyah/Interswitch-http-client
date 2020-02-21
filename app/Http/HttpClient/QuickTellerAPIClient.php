<?php

namespace App\Http\HttpClient;

class QuickTellerAPIClient extends HttpClient
{
    private $urlResource;

    /**
     * QuickTellerAPIClient constructor.
     */
    public function __construct($urlResource, $transactionParams = "")
    {
        parent::__construct($transactionParams);

        $this->addTimestamp();
        $this->addNonce();
        $this->baseUri($urlResource);
        $this->urlResource = $urlResource;
    }

    /**
     * Make the http request
     * If the request fails, retry a number of times before giving up
     * @param string $method
     * @param array $arguments
     * @return Data\Utils\HttpResponse|mixed
     * @throws \Exception
     */
    protected function makeRequest($method, $arguments)
    {
        return retry(5, function () use ($method, $arguments) {
            return parent::makeRequest($method, $arguments);
        }, 1);
    }

    /**
     * Retrieves a list payment items
     */
    public function quickTeller($endpoint)
    {
        $url = $this->urlResource . $endpoint;
        $this->addSignature('GET', $url);

        return $this->get($endpoint)->data();
    }

    /**
     * Retrieves a list payment items
     */
    public function svaPayments($endpoint, $data)
    {
        $url = $this->urlResource . $endpoint;
        $this->addSignature('POST', $url);

        return $this->post($endpoint, ['json' => $data])->data();
    }
}
