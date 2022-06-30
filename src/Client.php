<?php

declare(strict_types=1);

namespace SharkMachine\Lib\StopForumSpan;

use Exception;
use ParagonIE\HiddenString\HiddenString;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use SharkMachine\Lib\StopForumSpan\Exception\UnableToParseDateTimeException;
use SharkMachine\Lib\StopForumSpan\Exception\UnableToParseResponseException;
use SimpleXMLElement;

class Client
{
    private const METHOD_GET = 'GET';

    /**
     * @var string
     */
    private string $url = 'https://api.stopforumspam.org/api';

    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    /**
     * @var RequestFactoryInterface
     */
    private RequestFactoryInterface $requestFactory;

    /**
     * @var UriFactoryInterface
     */
    private UriFactoryInterface $uriFactory;

    /**
     * @var HiddenString|null
     */
    private ?HiddenString $apiKey;

    /**
     * @param ClientInterface         $client
     * @param RequestFactoryInterface $requestFactory
     * @param UriFactoryInterface     $uriFactory
     * @param HiddenString|null       $apiKey
     * @param string|null             $url
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        ?HiddenString $apiKey = null,
        ?string $url = null
    ) {
        if (null !== $url) {
            $this->url = $url;
        }
        $this->client         = $client;
        $this->requestFactory = $requestFactory;
        $this->uriFactory     = $uriFactory;
        $this->apiKey         = $apiKey;
    }

    /**
     * @param string $ipAddress
     *
     * @return Response
     * @throws ClientExceptionInterface
     * @throws UnableToParseDateTimeException
     * @throws UnableToParseResponseException
     */
    public function ipAddressInList(string $ipAddress): Response
    {
        $uri      = $this->uriFactory->createUri($this->url)->withQuery('ip=' . urlencode($ipAddress));
        $request  = $this->requestFactory->createRequest(self::METHOD_GET, $uri);
        return $this->parseResponse($this->client->sendRequest($request));
    }

    /**
     * @param string $ipAddress
     *
     * @return Response
     * @throws ClientExceptionInterface
     * @throws UnableToParseDateTimeException
     * @throws UnableToParseResponseException
     */
    public function emailInList(string $ipAddress): Response
    {
        $uri      = $this->uriFactory->createUri($this->url)->withQuery('email=' . urlencode($ipAddress));
        $request  = $this->requestFactory->createRequest(self::METHOD_GET, $uri);
        return $this->parseResponse($this->client->sendRequest($request));
    }

    /**
     * @param string $ipAddress
     *
     * @return Response
     * @throws ClientExceptionInterface
     * @throws UnableToParseDateTimeException
     * @throws UnableToParseResponseException
     */
    public function usernameInList(string $ipAddress): Response
    {
        $uri      = $this->uriFactory->createUri($this->url)->withQuery('username=' . urlencode($ipAddress));
        $request  = $this->requestFactory->createRequest(self::METHOD_GET, $uri);
        return $this->parseResponse($this->client->sendRequest($request));
    }

    /**
     * @param string $username
     * @param string $ipAddress
     * @param string $email
     * @param string $evidence
     *
     * @return void
     * @throws ClientExceptionInterface
     */
    public function addToDatabase(string $username, string $ipAddress, string $email, string $evidence): void
    {
        $uri = $this->uriFactory->createUri($this->url)->withQuery(
            http_build_query(
                [
                    'username' => $username,
                    'ip_addr'  => $ipAddress,
                    'email'    => $email,
                    'evidence' => $evidence,
                    'api_key'  => $this->apiKey->getString(),
                ]
            )
        );

        $this->client->sendRequest($this->requestFactory->createRequest(self::METHOD_GET, $uri));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Response
     * @throws UnableToParseDateTimeException
     * @throws UnableToParseResponseException
     */
    private function parseResponse(ResponseInterface $response): Response
    {
        $body = $response->getBody()->getContents();
        try {
            $xml = new SimpleXMLElement($body);
        } catch (Exception $ex) {
            throw UnableToParseResponseException::createFromException($ex);
        }
        return new Response(
            (string)$xml->type,
            'yes' === (string) $xml->appears,
            (string)$xml->lastseen,
            (int) $xml->frequency
        );
    }
}
