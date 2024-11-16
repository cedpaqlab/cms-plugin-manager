<?php
namespace Cedpaq\PluginManager\Factory;

use Cedpaq\PluginManager\Plugins\PluginInterface;
use Cedpaq\PluginManager\Exceptions\PluginNotFoundException;

class PluginFactory {
    public static function create($type, $config, $manager): PluginInterface {
        $class = "Cedpaq\\PluginManager\\Plugins\\Plugins\\{$type}Plugin";
        if (class_exists($class)) {
            return new $class($config, $manager);
        }
        throw new PluginNotFoundException($type);
    }
}
