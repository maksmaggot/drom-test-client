<?php


namespace Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

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

            $this->validateResponseStatusCodeOrFail($response, 200);

            $decoded = json_decode($response->getBody()->getContents(), true);

            if (!empty ($decoded['data'])) {
                foreach ($decoded['data'] as $item) {
                    $comments[] = new Comment($item['id'], $item['name'], $item['text']);
                }
            }
            return $comments;

        } catch (GuzzleException $e) {
            throw new \Exception("HttpClient error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws \Exception
     */
    public function create(string $name, string $text): Comment
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

            $this->validateResponseStatusCodeOrFail($response, 201);

            $decoded = json_decode($response->getBody()->getContents(), true);
            return new Comment($decoded['data']['id'], $decoded['data']['name'], $decoded['data']['text']);

        } catch (GuzzleException $e) {
            throw new \Exception("HttpClient error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws \Exception
     */
    public function update(int $id, string $name, string $text): Comment
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

            $this->validateResponseStatusCodeOrFail($response, 200);

            $decoded = json_decode($response->getBody()->getContents(), true);
            return new Comment($decoded['data']['id'], $decoded['data']['name'], $decoded['data']['text']);

        } catch (GuzzleException $e) {
            throw new \Exception("HttpClient error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param ResponseInterface $response
     * @param int $expected
     * @return bool
     * @throws \Exception
     */
    private function validateResponseStatusCodeOrFail(ResponseInterface $response, int $expected): bool
    {
        if ($response->getStatusCode() !== $expected) {
            throw new \Exception(
                "Invalid response status code : " . $response->getStatusCode() .
                " reason: " . $response->getReasonPhrase()
            );
        }
        return true;
    }
}