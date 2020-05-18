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
            return $this->client->getList();
        } catch (\Exception $e) {
            throw new \Exception("CommentClient Error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param Comment $comment
     * @throws \Exception
     */
    public function add(Comment $comment): void
    {
        try {
            $this->client->create($comment->name, $comment->text);
        } catch (\Exception $e) {
            throw new \Exception("CommentClient Error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param Comment $comment
     * @throws \Exception
     */
    public function update(Comment $comment): void
    {
        try {
            $this->client->update($comment->id, $comment->name, $comment->text);
        } catch (\Exception $e) {
            throw new \Exception("CommentClient Error: " . $e->getMessage(), 0, $e);
        }
    }
}