<?php


namespace Client;


class Comment
{
    /**
     * @var $id ?int
     */
    public $id;

    /**
     * @var $name string
     */
    public $name;

    /**
     * @var $text string
     */
    public $text;

    /**
     * Comment constructor.
     * @param int $id
     * @param string $name
     * @param string $text
     */
    public function __construct(?int $id, string $name, string $text)
    {
        $this->name = $name;
        $this->text = $text;
        $this->id = $id;
    }
}