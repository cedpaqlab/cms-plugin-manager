<?php
namespace Cedpaq\PluginManager\Singleton;

trait SingletonTrait {
    private static $instance;

    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __clone() {}
    private function __wakeup() {}
}
