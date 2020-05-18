<?php

namespace Tests;

use Client\Comment;
use Client\RestCommentClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RestCommentClientTest extends TestCase
{
    /**
     * @dataProvider getListDataProvider
     * @param Response $response
     * @param array $expected
     * @param string $message
     * @throws \Exception
     */
    public function testGetList(Response $response, array $expected, string $message)
    {
        $handlerStack = HandlerStack::create(new MockHandler([$response]));
        $restClient = new RestCommentClient(new Client(['handler' => $handlerStack]));

        $this->assertEquals($expected, $restClient->getList(), $message);
    }

    public function getListDataProvider()
    {
        return [
            [
                new Response(200, [], json_encode(['data' => []])),
                [],
                "GetList: empty data response"
            ],
            [
                new Response(200, [],
                    json_encode(['data' => [['id' => 1, 'name' => 'Michael', 'text' => 'text']]]),
                ),
                [new Comment(1, 'Michael', 'text')],
                "GetList: single entity response"
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
                "GetList: multiple entities response"
            ],
        ];
    }

    public function testResponseStatusCodeExceptionGetList()
    {
        $handlerStack = HandlerStack::create(new MockHandler([new Response(403)]));
        $restClient = new RestCommentClient(new Client(['handler' => $handlerStack]));

        $this->expectException(\Exception::class);
        $restClient->getList();
    }

    /**
     * @dataProvider updateDataProvider
     * @param Response $response
     * @param string $method
     * @param string $body
     * @param array $data
     * @throws \Exception
     */
    public function testUpdate(Response $response, string $method, string $body, array $data)
    {
        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create(new MockHandler([$response]));

        $handlerStack->push($history);
        $restClient = new RestCommentClient(new Client(['handler' => $handlerStack]));
        $restClient->update(...$data);

        foreach ($container as $transaction) {
            $this->assertEquals($method, $transaction['request']->getMethod(), "update method assertion");
            $this->assertEquals($body, (string)$transaction['request']->getBody(), "request body assertion");
        }

    }

    public function testResponseStatusCodeExceptionUpdate()
    {
        $handlerStack = HandlerStack::create(new MockHandler([new Response(403)]));
        $restClient = new RestCommentClient(new Client(['handler' => $handlerStack]));

        $this->expectException(\Exception::class);
        $restClient->update(1,'exept', 'sed');
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


    /**
     * @dataProvider createDataProvider
     * @param Response $response
     * @param string $method
     * @param string $body
     * @param array $data
     * @throws \Exception
     */
    public function testCreate(Response $response, string $method, string $body, array $data)
    {
        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create(new MockHandler([$response]));

        $handlerStack->push($history);
        $restClient = new RestCommentClient(new Client(['handler' => $handlerStack]));
        $restClient->create(...$data);

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
        $handlerStack = HandlerStack::create(new MockHandler([new Response(403)]));
        $restClient = new RestCommentClient(new Client(['handler' => $handlerStack]));

        $this->expectException(\Exception::class);
        $restClient->create('exept', 'sed');
    }

}
