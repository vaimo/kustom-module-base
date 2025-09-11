<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Api\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Base\Api\ServiceInterface;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Psr\Http\Message\ResponseInterface;
use Klarna\Logger\Model\Api\Container;
use Klarna\Logger\Model\Api\Logger;

/**
 * @internal
 */
class Service implements ServiceInterface
{

    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_NO_CONTENT = 204;
    public const REQUEST_TIMEOUT = 4;

    /**
     * Holds headers to be sent in HTTP request
     *
     * @var array
     */
    private $headers = [];
    /**
     * The base URL to interact with
     *
     * @var string
     */
    private $uri = '';
    /**
     * @var string
     */
    private $username = '';
    /**
     * @var string
     */
    private $password = '';
    /**
     * @var Client
     */
    private $client;
    /**
     * @var LoggerInterface $log
     */
    private $log;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Container
     */
    private $loggerContainer;

    /**
     * @param LoggerInterface $log
     * @param Container       $loggerContainer
     * @param Logger          $logger
     * @param Client          $client
     * @codeCoverageIgnore
     */
    public function __construct(
        LoggerInterface $log,
        Container $loggerContainer,
        Logger $logger,
        Client $client
    ) {
        $this->log = $log;
        $this->logger = $logger;
        $this->loggerContainer = $loggerContainer;
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function setUserAgent($product, $version, $mageInfo): void
    {
        $baseUA = sprintf('PHP/%s', PHP_VERSION);
        $this->setHeader(
            'User-Agent',
            sprintf('%s/%s;%s (%s)', $product, $version, $baseUA, $mageInfo)
        );
    }

    /**
     * @inheritdoc
     */
    public function setHeader($header, $value = null): void
    {
        if (!$value) {
            unset($this->headers[$header]);
            return;
        }
        $this->headers[$header] = $value;
    }

    /**
     * Getting back the request timeout value. Its public so that its value can be changed by a plugin.
     *
     * @return int
     */
    public function getRequestTimeoutValue(): int
    {
        return self::REQUEST_TIMEOUT;
    }

    /**
     * @inheritdoc
     */
    public function makeRequest(
        string $url,
        string $service,
        array $body = [],
        string $method = ServiceInterface::POST,
        ?string $klarnaId = null,
        ?string $action = null
    ): array {
        $response = [
            'is_successful' => false
        ];
        try {
            $data = [
                'headers' => $this->headers,
                'json'    => $body
            ];
            $data = $this->getAuth($data);

            $this->loggerContainer->setKlarnaId($klarnaId);
            $this->loggerContainer->setUrl($url);
            $this->loggerContainer->setRequest($body);
            $this->loggerContainer->setMethod($method);
            $this->loggerContainer->setService($service);

            /** @var ResponseInterface $response */
            $response = $this->client->$method(
                $this->uri . $url,
                $data,
                [
                    'connect_timeout' => $this->getRequestTimeoutValue(),
                    'timeout' => $this->getRequestTimeoutValue()
                    ],
            );
            $response = $this->processResponse($response);

            if (!$klarnaId) {
                $klarnaId = $this->getKlarnaIdFromResponse($response);
            }
            $this->loggerContainer->setKlarnaId($klarnaId);

            $response['is_successful'] = true;
        } catch (BadResponseException $e) {
            $this->log->critical($e);
            $response['response_status_code'] = $e->getCode();
            $response['response_status_message'] = $e->getMessage();
            $response = $this->processResponse($response);
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                $this->log->error($errorResponse->getStatusCode() . ' ' . $errorResponse->getReasonPhrase());
                try {
                    $body = $this->processResponse($errorResponse);
                } catch (\Exception $e) {
                    $this->log->critical($e);
                    $response['exception_code'] = $e->getCode();
                }
                $response = array_merge($response, $body);
            }
            $response['exception_code'] = $e->getCode();
        } catch (\Exception $e) {
            $this->log->critical($e);
            $response['exception_code'] = $e->getCode();
        }

        $this->loggerContainer->setAction($action);
        $this->loggerContainer->setResponse($response);
        $this->logger->logContainer($this->loggerContainer);
        return $response;
    }

    /**
     * Set auth data if username or password has been provided
     *
     * @param array $data
     * @return mixed
     */
    private function getAuth(array $data)
    {
        if ($this->username || $this->password) {
            $data['auth'] = [$this->username, $this->password];
        }
        return $data;
    }

    /**
     * Process the response and return an array
     *
     * @param ResponseInterface|array $response
     * @return array
     * @throws \Klarna\Base\Model\Api\Exception
     */
    private function processResponse($response)
    {
        if (is_array($response)) {
            return $response;
        }
        try {
            $body = json_decode($response->getBody()->__toString(), true);
            $data = $body;
        } catch (\Exception $e) {
            $body = [];
            $data = [
                'exception' => $e->getMessage()
            ];
        }
        if ($response->getStatusCode() === self::HTTP_UNAUTHORIZED) {
            throw new KlarnaApiException(__($response->getReasonPhrase()));
        }
        $data['response_object'] = [
            'headers' => $response->getHeaders(),
            'body'    => $body
        ];
        $data['response_status_code'] = $response->getStatusCode();
        $data['response_status_message'] = $response->getReasonPhrase();
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function connect($username, $password, $connectUrl = null): bool
    {
        $this->username = $username;
        $this->password = $password;
        if ($connectUrl) {
            $this->uri = $connectUrl;
        }
        return true;
    }

    /**
     * Getting the Klarna id from the response
     *
     * @param array $response
     * @return string
     */
    private function getKlarnaIdFromResponse(array $response)
    {
        foreach (['session_id', 'order_id'] as $idField) {
            if (isset($response[$idField])) {
                return $response[$idField];
            }
        }
        return null;
    }
}
