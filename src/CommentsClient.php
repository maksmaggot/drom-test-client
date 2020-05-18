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
     * @return Comment
     */
    public function create(string $name, string $text): Comment;

    /**
     * @param int $id
     * @param string $name
     * @param string $text
     * @return Comment
     */
    public function update(int $id, string $name, string $text): Comment;
}