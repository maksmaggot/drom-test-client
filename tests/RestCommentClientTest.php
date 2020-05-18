<?php

namespace Tests;

use Client\Comment;
use Client\RestCommentClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RestCommentClientTest extends TestCase
{
    /**
     * @var RestCommentClient
     */
    private $restClient;

    /**
     * @var MockHandler
     */
    private $mock;

    protected function setUp(): void
    {
        $this->mock = new MockHandler();
        $handlerStack = HandlerStack::create($this->mock);
        $client = new Client(['handler' => $handlerStack]);
        $this->restClient = new RestCommentClient($client);
    }

    /**
     * @dataProvider getListDataProvider
     * @param Response $response
     * @param array $expected
     * @throws \Exception
     */
    public function testGetList(Response $response, array $expected)
    {
        $this->mock->reset();
        $this->mock->append($response);

        $this->assertEquals($expected, $this->restClient->getList());
    }

    /**
     * @return array[]
     */
    public function getListDataProvider()
    {
        return [
            [
                new Response(200, [], json_encode(['data' => []])),
                []
            ],
            [
                new Response(200, [],
                    json_encode(['data' => [['id' => 1, 'name' => 'Michael', 'text' => 'text']]])
                ),
                [new Comment(1, 'Michael', 'text')]
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
                [new Comment(1, 'Michael', 'text'), new Comment(2, 'Rachel', 'text2')]
            ],
        ];
    }

    /**
     * @dataProvider updateDataProvider
     * @param Response $response
     * @param Comment $expected
     * @param array $data
     * @throws \Exception
     */
    public function testUpdate(Response $response, Comment $expected , array $data)
    {
        $this->mock->reset();
        $this->mock->append($response);

        $this->assertEquals($expected, $this->restClient->update(...$data));
    }

    public function updateDataProvider()
    {
        return [
            [
                new Response(200, [],
                    json_encode(['data' => ['id' => 1, 'name' => 'Micha', 'text' => 'textnew']])
                ),
                new Comment(1, 'Micha', 'textnew'),
                [1, 'Micha', 'textnew']
            ],
        ];
    }


    /**
     * @dataProvider createDataProvider
     * @param Response $response
     * @param Comment $expected
     * @param array $data
     * @throws \Exception
     */
    public function testCreate(Response $response, Comment $expected , array $data)
    {
        $this->mock->reset();
        $this->mock->append($response);

        $this->assertEquals($expected, $this->restClient->create(...$data));
    }

    public function createDataProvider()
    {
        return [
            [
                new Response(201, [],
                    json_encode(['data' => ['id' => 15, 'name' => 'Max', 'text' => 'textnewtext']])
                ),
                new Comment(15, 'Max', 'textnewtext'),
                [15, 'Micha', 'textnewtext'],
            ],
        ];
    }

    public function testResponseStatusCodeExceptionGetList()
    {
        $this->mock->reset();
        $this->mock->append(new Response(403));

        $this->expectException(\Exception::class);
        $this->restClient->getList();
    }
}
