<?php


namespace Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class RestCommentClient implements CommentsClient
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
     * @throws \Exception
     */
    public function getList(): array
    {
        try {
            $comments = [];
            $response = $this->httpClient->request('GET', '/comments');

            if ($response->getStatusCode() !== 200) {
                throw new \Exception("Invalid response status code : " . $response->getStatusCode());
            }

            $decoded = json_decode($response->getBody()->getContents(), true);
            if (empty ($decoded['data'])) {
                return [];
            }

            foreach ($decoded['data'] as $item) {
                $comments[] = new Comment($item['id'], $item['name'], $item['text']);
            }
            return $comments;

        } catch (GuzzleException $e) {
            throw new \Exception("HttpClient error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $name
     * @param string $text
     * @throws \Exception
     */
    public function create(string $name, string $text): void
    {
        try {
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

            if ($response->getStatusCode() !== 201) {
                throw new \Exception("Invalid response status code : " . $response->getStatusCode());
            }
        } catch (GuzzleException $e) {
            throw new \Exception("HttpClient error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $text
     * @throws \Exception
     */
    public function update(int $id, string $name, string $text): void
    {
        try {
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

            if ($response->getStatusCode() !== 200) {
                throw new \Exception("Invalid response status code : " . $response->getStatusCode());
            }

        } catch (GuzzleException $e) {
            throw new \Exception("HttpClient error: " . $e->getMessage(), 0, $e);
        }
    }
}