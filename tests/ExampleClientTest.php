<?php

namespace Tests;


use Client\Comment;
use Client\DIContainer;
use Client\ExampleClient;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ExampleClientTest extends TestCase
{
    /**
     * @dataProvider getCommentsProvider
     * @param Response $response
     * @param array $expected
     * @param string $message
     * @throws \Exception
     */
    public function testGetComments(Response $response, array $expected, string $message)
    {
        $mock = new MockHandler([$response]);
        DIContainer::getContainer()->set(ClientInterface::class, function () use ($mock) {
            $handlerStack = HandlerStack::create($mock);
            return new Client(['handler' => $handlerStack]);
        });

        $exampleClient = new ExampleClient();
        $this->assertEquals($expected, $exampleClient->getComments(), $message);
    }

    public function getCommentsProvider()
    {
        return [
            [
                new Response(200, [], json_encode(['data' => []])),
                [],
                "getComments: empty data"
            ],
            [
                new Response(200, [],
                    json_encode(['data' => [['id' => 1, 'name' => 'Michael', 'text' => 'text']]]),
                ),
                [new Comment(1, 'Michael', 'text')],
                "getComments: single entity"
            ],
            [
                new Response(
                    200,
                    [],
                    json_encode(
                        [
                            'data' =>
                                [
                                    ['id' => 1, 'name' => 'Michael', 'text' => 'text'],
                                    ['id' => 2, 'name' => 'Rachel', 'text' => 'text2'],
                                ]
                        ])),
                [new Comment(1, 'Michael', 'text'), new Comment(2, 'Rachel', 'text2')],
                "getComments: multiple entities"
            ],
        ];
    }

    public function testResponseStatusCodeExceptionGetList()
    {
        DIContainer::getContainer()->set(ClientInterface::class, function () {
            $handlerStack = HandlerStack::create(new MockHandler([new Response(403)]));
            return new Client(['handler' => $handlerStack]);
        });
        $exampleClient = new ExampleClient();

        $this->expectException(\Exception::class);
        $exampleClient->getComments();
    }

    /**
     * @dataProvider createDataProvider
     * @param Response $response
     * @param string $body
     * @param array $data
     * @throws \Exception
     */
    public function testCreateComment(Response $response, string $body, array $data)
    {
        $container = [];
        DIContainer::getContainer()->set(ClientInterface::class, function () use ($response, &$container) {
            $history = Middleware::history($container);
            $handlerStack = HandlerStack::create(new MockHandler([$response]));
            $handlerStack->push($history);
            return new Client(['handler' => $handlerStack]);
        });

        $exampleClient = new ExampleClient();
        $exampleClient->createComment(...$data);

        $this->assertRequest($body, $container);
    }

    public function createDataProvider()
    {
        return [
            [
                new Response(201),
                'POST',
                json_encode(['name' => 'Max', 'text' => 'textnewtext']),
                ['Max', 'textnewtext'],
            ],
        ];
    }

    public function testResponseStatusCodeExceptionCreate()
    {
        DIContainer::getContainer()->set(ClientInterface::class, function () {
            $handlerStack = HandlerStack::create(new MockHandler([new Response(403)]));
            return new Client(['handler' => $handlerStack]);
        });
        $exampleClient = new ExampleClient();

        $this->expectException(\Exception::class);
        $exampleClient->createComment('exept', 'sed');
    }

    /**
     * @dataProvider updateDataProvider
     * @param Response $response
     * @param string $body
     * @param array $data
     * @throws \Exception
     */
    public function testUpdateComment(Response $response,  string $body, array $data)
    {
        $container = [];
        DIContainer::getContainer()->set(ClientInterface::class, function () use ($response, &$container) {
            $history = Middleware::history($container);
            $handlerStack = HandlerStack::create(new MockHandler([$response]));
            $handlerStack->push($history);
            return new Client(['handler' => $handlerStack]);
        });

        $exampleClient = new ExampleClient();
        $exampleClient->updateComment(...$data);

        $this->assertRequest($body, $container);
    }

    public function updateDataProvider()
    {
        return [
            [
                new Response(200),
                'PUT',
                json_encode(['name' => "Micha", 'text' => "textnew"]),
                [1, 'Micha', 'textnew']
            ],
        ];
    }

    private function assertRequest(string $body, array $container)
    {
        foreach ($container as $transaction) {
            $this->assertEquals($body, (string)$transaction['request']->getBody(), "create body assertion");
        }
    }
}
