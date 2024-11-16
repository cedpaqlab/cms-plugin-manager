<?php
namespace Cedpaq\PluginManager\Config;

use Cedpaq\PluginManager\Singleton\SingletonTrait;

class AppConfig {
    use SingletonTrait;

    private $config = [];

    public function get($key) {
        return $this->config[$key] ?? null;
    }

    public function set($key, $value) {
        this->config[$key] = $value;
    }
}
