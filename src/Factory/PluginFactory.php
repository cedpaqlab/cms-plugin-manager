<?php
namespace Cedpaq\PluginManager\Factory;

use Cedpaq\PluginManager\Plugins\PluginInterface;

class PluginFactory {
    public static function create($type, $config): PluginInterface {
        $class = "Cedpaq\\PluginManager\\Plugins\\Plugins\\{$type}Plugin";
        if (class_exists($class)) {
            return new $class($config);
        }
        throw new \Exception("Plugin type {$type} not found.");
    }
}
