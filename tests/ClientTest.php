<?php

/**
 * Exception inspections not applicable in test classes
 *
 * @noinspection PhpDocMissingThrowsInspection PhpUnhandledExceptionInspection
 */

declare(strict_types=1);

namespace SharkMachine\Lib\StopForumSpan\Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SharkMachine\Lib\StopForumSpan\Client;

final class ClientTest extends AbstractBaseTest
{
    /**
     * @return void
     */
    public function testIpAddressInList(): void
    {
        $param = 'ip';
        $value = '127.0.0.1';
        $httpClient = $this->getMockedClient(
            $this->getMockedResponse($param, false),
            $param,
            $value
        );
        $psrFactory = new Psr17Factory();
        $client = new Client($httpClient, $psrFactory, $psrFactory);
        $response = $client->ipAddressInList('127.0.0.1');
        self::assertFalse($response->isAppears());

        $httpClient = $this->getMockedClient(
            $this->getMockedResponse($param, true),
            $param,
            $value
        );
        $psrFactory = new Psr17Factory();
        $client = new Client($httpClient, $psrFactory, $psrFactory);
        $response = $client->ipAddressInList('127.0.0.1');
        self::assertTrue($response->isAppears());
    }

    /**
     * @return void
     */
    public function testEmailInList(): void
    {
        $param = 'email';
        $value = 'email@email@com';
        $httpClient = $this->getMockedClient(
            $this->getMockedResponse($param, false),
            $param,
            $value
        );
        $psrFactory = new Psr17Factory();
        $client = new Client($httpClient, $psrFactory, $psrFactory);
        $response = $client->emailInList('email@email@com');
        self::assertFalse($response->isAppears());

        $httpClient = $this->getMockedClient(
            $this->getMockedResponse($param, true),
            $param,
            $value
        );
        $psrFactory = new Psr17Factory();
        $client = new Client($httpClient, $psrFactory, $psrFactory);
        $response = $client->emailInList('email@email@com');
        self::assertTrue($response->isAppears());
    }

    /**
     * @return void
     */
    public function testUsernameInList(): void
    {
        $param = 'username';
        $value = 'SpamUsername';
        $httpClient = $this->getMockedClient(
            $this->getMockedResponse($param, false),
            $param,
            $value
        );
        $psrFactory = new Psr17Factory();
        $client = new Client($httpClient, $psrFactory, $psrFactory);
        $response = $client->usernameInList('SpamUsername');
        self::assertFalse($response->isAppears());

        $httpClient = $this->getMockedClient(
            $this->getMockedResponse($param, true),
            $param,
            $value
        );
        $psrFactory = new Psr17Factory();
        $client = new Client($httpClient, $psrFactory, $psrFactory);
        $response = $client->usernameInList('SpamUsername');
        self::assertTrue($response->isAppears());
    }

    /**
     * @param ResponseInterface $response
     * @param string            $type
     * @param string            $data
     *
     * @return ClientInterface&MockObject
     */
    private function getMockedClient(ResponseInterface $response, string $type, string $data): ClientInterface
    {
        $client = $this->createMock(ClientInterface::class);
        // @todo Add checking for request
        $client->expects(self::atLeastOnce())->method('sendRequest')
            ->with(
                self::callback(
                    static function (RequestInterface $request) use ($type, $data): bool {
                        $params = http_build_query([$type => $data]);
                        self::assertSame(
                            'https://api.stopforumspam.org/api?' . $params,
                            $request->getUri()->__toString()
                        );
                        return true;
                    }
                )
            )
            ->willReturn($response);

        return $client;
    }

    /**
     * @param string $type
     * @param bool   $appears
     *
     * @return ResponseInterface&MockObject
     */
    private function getMockedResponse(string $type, bool $appears): ResponseInterface
    {
        $appearsString = $appears ? 'yes' : 'no';
        $xmlMessage = <<<XML
            <response success="true">
                <type>$type</type>
                <appears>$appearsString</appears>
                <lastseen>2007-09-18 05:48:53</lastseen>
                <frequency>2</frequency>
            </response>
        XML;
        $message = $this->createMock(StreamInterface::class);
        $message->expects(self::atLeastOnce())->method('getContents')
            ->willReturn($xmlMessage);
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::atLeastOnce())->method('getBody')
            ->willReturn($message);

        return $response;
    }
}
