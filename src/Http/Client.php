<?php

declare(strict_types=1);

namespace CamooPay\Http;

use CamooPay\Collection\ResponseCollection;
use CamooPay\Constant\Config;
use CamooPay\Exception\CamooPayException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\ApiException;
use Maviance\S3PApiClient\Configuration;
use Maviance\S3PApiClient\HeaderSelector;
use Maviance\S3PApiClient\ObjectSerializer;
use stdClass;

class Client
{
    private const POST_REQUEST = 'POST';

    private const GET_REQUEST = 'GET';

    protected HeaderSelector $headerSelector;

    private Configuration $config;

    private ApiClient $client;

    private string $modelName;

    public function __construct(ApiClient $client, Configuration $config, string $modelName)
    {
        $this->headerSelector = new HeaderSelector();
        $this->config = $config;
        $this->client = $client;
        $this->modelName = $modelName;
    }

    public function post(string $url, array $data = []): Response
    {
        return $this->sendRequest(self::POST_REQUEST, $url, $data);
    }

    public function get(string $url, array $data = []): Response
    {
        return $this->sendRequest(self::GET_REQUEST, $url, $data);
    }

    protected function getRequest(
        string $type,
        string $resourcePath,
        array $data = [],
        ?string $xApiVersion = null
    ): Request {
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';

        $headerParams['x-api-version'] = ObjectSerializer::toHeaderValue($xApiVersion);

        $headers = $this->headerSelector->selectHeaders(
            ['application/json'],
            []
        );

        if (!empty($data)) {
            if ($type === self::GET_REQUEST) {
                $queryParams = $data;
            } else {
                $httpBody = is_array($data) ? (object)$data : $data;
                if ($httpBody instanceof stdClass && $headers['Content-Type'] === 'application/json') {
                    $httpBody = \GuzzleHttp\json_encode($httpBody);
                }
            }
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = http_build_query($queryParams);

        return new Request(
            $type,
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    protected function sendRequest(
        string $type,
        string $resourcePath,
        array $data = [],
        ?string $xApiVersion = null
    ): Response {
        $returnType = $this->getReturnType();
        $xApiVersion = $xApiVersion ?? Config::API_VERSION;
        $request = $this->getRequest($type, $resourcePath, $data, $xApiVersion);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();

            $content = $responseBody->getContents();

            if (!in_array($returnType, ['string', 'integer', 'bool'])) {
                $content = json_decode($content);
            }
            $contentResponse = is_object($content) ? [$content] : $content;

            return new Response(
                ResponseCollection::create($contentResponse, $returnType),
                $response->getStatusCode(),
                $response->getHeaders()
            );
        } catch (ApiException $exception) {
            throw new CamooPayException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    protected function createHttpClientOption(): array
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new CamooPayException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }

    private function getReturnType(): string
    {
        return $this->modelName !== 'object' ? '\\Maviance\\S3PApiClient\Model\\' . $this->modelName : 'object';
    }
}
