<?php

namespace CartBoss\Api\Managers;

use CartBoss\Api\Exceptions\ApiException;
use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class ApiClient
{
    /**
     * Version of our client.
     */
    const CLIENT_VERSION = "2.0.0";

    /**
     * Endpoint of the remote API.
     */
//    const API_ENDPOINT = "https://api.cartboss.io";
    const API_ENDPOINT = "http://127.0.0.1:8082/";

    /**
     * Version of the remote API.
     */
    const API_VERSION = "2";

    /**
     * HTTP Methods
     */
    const HTTP_GET = "GET";
    const HTTP_POST = "POST";
    const HTTP_DELETE = "DELETE";
    const HTTP_PATCH = "PATCH";

    /**
     * Default response timeout (in seconds).
     */
    const DEFAULT_TIMEOUT = 5;

    /**
     * Default connect timeout (in seconds).
     */
    const DEFAULT_CONNECT_TIMEOUT = 2;

    /**
     * HTTP status code for an empty ok response.
     */
    const HTTP_NO_CONTENT = 204;
    /**
     * @var array
     */
    protected $version_strings = [];
    /**
     * @var Client
     */
    private $httpClient;
    /**
     * @var string
     */
    private $api_key;

    public function __construct(string $api_key, $timeout=null, $connect_timeout=null)
    {
        $this->api_key = trim($api_key);

        $this->httpClient = new Client([
            RequestOptions::TIMEOUT => $timeout ?? self::DEFAULT_TIMEOUT,
            RequestOptions::CONNECT_TIMEOUT => $connect_timeout ?? self::DEFAULT_CONNECT_TIMEOUT,
            RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
        ]);

        $this->addVersionString("CartBoss/API/" . self::CLIENT_VERSION);
        $this->addVersionString("PHP/" . phpversion());
    }

    public function addVersionString($versionString): ApiClient
    {
        $this->version_strings[] = str_replace([" ", "\t", "\n", "\r"], '-', $versionString);

        return $this;
    }

    /**
     * @throws ApiException
     */
    public function performHttpCall($httpMethod, $apiMethod, $httpBody = null): ?stdClass
    {
        if (empty($this->api_key)) {
            throw new ApiException("You have not set an API key.");
        }

        $url = self::API_ENDPOINT . "/" . self::API_VERSION . "/" . $apiMethod;

        $userAgent = implode(' ', $this->version_strings);

        $headers = [
            'Accept' => "application/json",
            'Authorization' => "Bearer {$this->api_key}",
            'User-Agent' => $userAgent,
        ];

        if ($httpBody !== null) {
            $headers['Content-Type'] = "application/json";
        }

        if (function_exists("php_uname")) {
            $headers['X-CartBoss-Client-Info'] = php_uname();
        }

        return $this->send($httpMethod, $url, $headers, $httpBody);
    }

    /**
     * @throws ApiException
     */
    public function send($httpMethod, $url, $headers, $httpBody): ?stdClass
    {
        $request = new Request($httpMethod, $url, $headers, $httpBody);

        try {
            $response = $this->httpClient->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {

            // Not all Guzzle Exceptions implement hasResponse() / getResponse()
            if (method_exists($e, 'hasResponse') && method_exists($e, 'getResponse')) {
                if ($e->hasResponse()) {
                    throw ApiException::createFromResponse($e->getResponse(), $request);
                }
            }

            throw new ApiException($e->getMessage(), $e->getCode(), $request, null);
        }

        if (!$response) {
            throw new ApiException("Did not receive API response.", 0, $request);
        }

        return $this->parseResponseBody($response);
    }

    /**
     * Parse the PSR-7 Response body
     *
     * @param ResponseInterface $response
     * @return stdClass|null
     * @throws ApiException
     */
    private function parseResponseBody(ResponseInterface $response): ?stdClass
    {
        $body = (string)$response->getBody();
        if (empty($body)) {
            if ($response->getStatusCode() === self::HTTP_NO_CONTENT) {
                return null;
            }

            throw new ApiException("No response body found.");
        }

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode CartBoss response: '{$body}'.");
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiException::createFromResponse($response, null);
        }

        return $object;
    }

    /**
     * @param array $body
     * @return null|string
     * @throws ApiException
     */
    public function parseRequestBody(array $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        try {
            $encoded = @json_encode($body);
        } catch (InvalidArgumentException $e) {
            throw new ApiException("Error encoding parameters into JSON: '" . $e->getMessage() . "'.");
        }

        return $encoded;
    }


}