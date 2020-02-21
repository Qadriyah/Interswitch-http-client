<?php

namespace App\Http\HttpClient;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use RuntimeException;
use Webpatser\Uuid\Uuid;

class HttpClient
{
    /**
     * Http response class
     * @var string
     */
    protected $responseClass = HttpResponse::class;

    private $options;
    protected $transactionParams;

    public function __construct($transactionParams = "")
    {
        $this->options = $this->getDefaultOptions();
        $this->transactionParams = $transactionParams;
    }

    /**
     * Handles any dynamic calls to this class
     *
     * @param $method
     * @param $arguments
     * @return Data\Utils\HttpResponse
     */
    public function __call($method, $arguments)
    {
        if (!in_array($method, ['get', 'put', 'post', 'options', 'head', 'delete', 'patch', 'request'], true)) {
            throw new RuntimeException('Call to Undefined method ' . $method);
        }
        return $this->makeRequest($method, $arguments);
    }

    /**
     * Gets the default client options
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'stream' => true,
            'connect_timeout' => 6000,
            'headers' => [
                'Content-Type' => 'Application/json',
                'SignatureMethod' => env('SIGNATURE_METHOD'),
                'Authorization' => 'InterswitchAuth ' . base64_encode(env('CLIENT_ID'))
            ],
        ];
    }

    /**
     * Generates and adds the signature to the options array
     *
     * @param $method
     * @param $urlResource
     * @return $this
     */
    public function addSignature($method, $urlResource)
    {
        $encodedUrl = urlencode(iconv('UTF-8', env('ISO_8859_1'), $urlResource));
        $signatureCipher = $method .
            "&" . $encodedUrl .
            "&" . $this->options['headers']['Timestamp'] .
            "&" . $this->options['headers']['Nonce'] .
            "&" . env('CLIENT_ID') .
            "&" . env('CLIENT_KEY');

        if (!empty($this->transactionParams) || $this->transactionParams !== "") {
            $signatureCipher .= "&" . $this->transactionParams;
        }
        $signature = base64_encode(hash(env('SIGNATURE_METHOD'), $signatureCipher, true));
        Arr::set($this->options, "headers.Signature", $signature);
        return $this;
    }

    /**
     * Generates the unix timestamp
     * @return $this
     * @throws \Exception
     */
    public function addTimestamp()
    {
        $date = new \DateTime(null, new \DateTimeZone("Africa/Kampala"));
        Arr::set($this->options, 'headers.Timestamp', $date->getTimestamp());
        return $this;
    }

    /**
     * Generates the nonce
     * @return $this
     * @throws \Exception
     */
    public function addNonce()
    {
        $nonce = str_replace('-', '', Uuid::generate()->string);
        Arr::set($this->options, 'headers.Nonce', $nonce);
        return $this;
    }

    /**
     * Adds the terminal id to the headers
     * @return $this
     * @throws \Exception
     */
    public function addTerminalId()
    {
        Arr::set($this->options, 'headers.TerminalId', env('TERMINAL_ID'));
        return $this;
    }

    /**
     * Sets the base url
     *
     * @param string $url
     * @return $this
     */
    public function baseUri($url)
    {
        Arr::set($this->options, 'base_uri', $url);

        return $this;
    }

    /**
     * Executes a request
     * @param string $method
     * @param array $arguments
     * @return Data\Utils\HttpResponse
     */
    protected function makeRequest($method, $arguments)
    {
        $response = (new Client($this->options))->{$method}(...$arguments);

        return new $this->responseClass($response, $this);
    }
}
