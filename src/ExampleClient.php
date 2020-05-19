<?php


namespace Client;


use GuzzleHttp\ClientInterface;

class ExampleClient
{
    /**
     * @var CommentsHttpRepository
     */
    private $repository;

    /**
     * ExampleClient constructor.
     */
    public function __construct()
    {
        $this->repository = DIContainer::getContainer()->get(CommentsHttpRepository::class);
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
     * @throws \Exception
     */
    public function createComment(string $name, string $text): void
    {
        $this->repository->add(new Comment(null, $name, $text));
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $text
     * @throws \Exception
     */
    public function updateComment(int $id, string $name, string $text): void
    {
        $this->repository->update(new Comment($id, $name, $text));
    }
}