<?php


namespace Client;

class ExampleHttpRepository
{
    /**
     * @var ExampleHttpClient
     */
    private $client;

    /**
     * ExampleHttpRepository constructor.
     * @param ExampleHttpClient $client
     */
    public function __construct(ExampleHttpClient $client)
    {
        $this->client = $client;
    }

    public function getList()
    {

    }

    public function add()
    {

    }

    public function update()
    {

    }
}