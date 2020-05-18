<?php


namespace Client;


class CommentsHttpRepository
{
    /**
     * @var CommentsClient
     */
    private $client;

    /**
     * CommentsHttpRepository constructor.
     * @param CommentsClient $client
     */
    public function __construct(CommentsClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return Comment[]
     * @throws \Exception
     */
    public function getList(): array
    {
        try {
            $this->client->getList();
        } catch (\Exception $e) {
            throw new \Exception("CommentClient Error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param $name
     * @param $text
     * @return Comment
     * @throws \Exception
     */
    public function add($name, $text): Comment
    {
        try {
            return $this->client->create($name, $text);
        } catch (\Exception $e) {
            throw new \Exception("CommentClient Error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $text
     * @return Comment
     * @throws \Exception
     */
    public function update($id, $name, $text): Comment
    {
        try {
            return $this->client->update($id, $name, $text);
        } catch (\Exception $e) {
            throw new \Exception("CommentClient Error: " . $e->getMessage(), 0, $e);
        }
    }
}