<?php


namespace Client;


class ExampleClient
{
    /**
     * @var CommentsHttpRepository
     */
    private $repository;

    /**
     * ExampleClient constructor.
     * @param CommentsHttpRepository $repository
     */
    public function __construct(CommentsHttpRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Comment[]
     * @throws \Exception
     */
    public function getComments(): array
    {
        return $this->repository->getList();
    }

    /**
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws \Exception
     */
    public function createComment(string $name, string $text): Comment
    {
        $this->repository->add($name, $text);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws \Exception
     */
    public function updateComment(int $id, string $name, string $text): Comment
    {
        $this->repository->update($id, $name, $text);
    }
}