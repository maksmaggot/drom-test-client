<?php


namespace Client;


class Comment
{
    /**
     * @var $id int
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
     * @param $id
     * @param $name
     * @param $text
     */
    public function __construct($id, $name, $text)
    {
        $this->name = $name;
        $this->text = $text;
        $this->id = $id;
    }
}