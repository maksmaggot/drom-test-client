<?php


namespace Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class DIContainer
{
    /** @var null|DIContainer $instance */
    private static $instance = null;

    private $objStorage = [];

    private function __construct()
    {
        $this->buildDependencies();
    }

    private function __clone()
    {
    }

    public static function getContainer(): DIContainer
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $interface)
    {
        if (!isset($this->objStorage[$interface])) {
            throw new \Exception("Not found realisation for interface {$interface}");
        }
        if (is_callable($this->objStorage[$interface])) {
            return $this->objStorage[$interface]($this);
        }
        return new $this->objStorage[$interface];
    }

    public function set(string $interface, $realisation)
    {
        $this->objStorage[$interface] = $realisation;
    }

    private function buildDependencies()
    {
        $this->objStorage[ClientInterface::class] = function () {
            return new Client(['base_uri' => 'https://example.com/']);
        };
        $this->objStorage[CommentsClient::class] = function (DIContainer $container) {
            return new RestCommentClient($container->get(ClientInterface::class));
        };
        $this->objStorage[CommentsHttpRepository::class] = function (DIContainer $container) {
            return new CommentsHttpRepository($container->get(CommentsClient::class));
        };
    }
}
