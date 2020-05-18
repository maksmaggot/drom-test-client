<?php


namespace Client;


interface CommentsClient
{
    /**
     * @return Comment[]
     */
    public function getList(): array;

    /**
     * @param string $name
     * @param string $text
     * @throws \Exception
     */
    public function create(string $name, string $text): void;

    /**
     * @param int $id
     * @param string $name
     * @param string $text
     * @throws \Exception
     */
    public function update(int $id, string $name, string $text): void;
}