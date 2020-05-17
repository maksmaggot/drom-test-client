<?php


namespace Client;


class ExampleHttpClient
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct( $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getList(): array
    {
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function update()
    {
        // TODO: Implement update() method.
    }
}