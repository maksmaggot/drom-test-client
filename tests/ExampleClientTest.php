<?php

namespace Tests;


use Client\Comment;
use Client\DIContainer;
use Client\ExampleClient;
use Client\RestCommentClient;
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
     * @var DIContainer
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = DIContainer::getContainer();
    }

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
        $this->container->set(ClientInterface::class, function () use ($mock) {
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
        $this->container->set(ClientInterface::class, function () {
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
     * @param string $method
     * @param string $body
     * @param array $data
     * @throws \Exception
     */
        public function testCreateComment(Response $response, string $method, string $body, array $data)
        {
            $container = [];
            $this->container->set(ClientInterface::class, function () use ($response, &$container){
                $history = Middleware::history($container);
                $handlerStack = HandlerStack::create(new MockHandler([$response]));
                $handlerStack->push($history);
                return new Client(['handler' => $handlerStack]);
            });

            $exampleClient = new ExampleClient();
            $exampleClient->createComment(...$data);

            foreach ($container as $transaction) {
                $this->assertEquals($method, $transaction['request']->getMethod(), "create method assertion");
                $this->assertEquals($body, (string)$transaction['request']->getBody(), "create body assertion");
            }
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
        $this->container->set(ClientInterface::class, function () {
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
     * @param string $method
     * @param string $body
     * @param array $data
     * @throws \Exception
     */
    public function testUpdateComment(Response $response, string $method, string $body, array $data)
    {
        $container = [];
        $this->container->set(ClientInterface::class, function () use ($response, &$container){
            $history = Middleware::history($container);
            $handlerStack = HandlerStack::create(new MockHandler([$response]));
            $handlerStack->push($history);
            return new Client(['handler' => $handlerStack]);
        });

        $exampleClient = new ExampleClient();
        $exampleClient->updateComment(...$data);

        foreach ($container as $transaction) {
            $this->assertEquals($method, $transaction['request']->getMethod(), "create method assertion");
            $this->assertEquals($body, (string)$transaction['request']->getBody(), "create body assertion");
        }
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
}
