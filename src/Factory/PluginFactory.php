<?php
namespace CMS\PluginSystem\Factory;

use CMS\PluginSystem\Plugins\PluginInterface;

class PluginFactory {
    public static function create($type, $config): PluginInterface {
        $class = "CMS\\PluginSystem\\Plugins\\Plugins\\{$type}Plugin";
        if (class_exists($class)) {
            return new $class($config);
        }
        throw new \Exception("Plugin type {$type} not found.");
    }
}
