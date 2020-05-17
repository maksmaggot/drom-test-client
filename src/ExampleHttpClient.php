<?php


namespace Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class ExampleHttpClient
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return Comment[]
     * @throws GuzzleException
     */
    public function getComments(): array
    {
        $response = $this->httpClient->request(
            'GET',
            '/comments'
        );

        $decoded = json_decode($response->getBody()->getContents(), true);
        if (!empty ($decoded['data'])) {
            $items = [];
            foreach ($decoded['data'] as $item) {
                $items[] = new Comment($item['id'], $item['name'], $item['text']);
            }
            return $items;
        }

        return [];
    }

    /**
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws GuzzleException
     */
    public function create(string $name, string $text): Comment
    {
        $response = $this->httpClient->request(
            'POST',
            '/comment',
            [
                'json' => [
                    "name" => $name,
                    "text" => $text
                ]
            ]
        );

        $decoded = json_decode($response->getBody()->getContents(), true);
        return new Comment($decoded['id'], $decoded['name'], $decoded['text']);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws GuzzleException
     */
    public function update(int $id, string $name, string $text): Comment
    {
        $response = $this->httpClient->request(
            'PUT',
            "/comment/$id",
            [
                'json' => [
                    "name" => $name,
                    "text" => $text
                ]
            ]
        );

        $decoded = json_decode($response->getBody()->getContents(), true);
        return new Comment($decoded['id'], $decoded['name'], $decoded['text']);
    }
}