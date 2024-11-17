<?php
namespace Cedpaq\PluginManager\Config;

interface ConfigRepositoryInterface {
    public function get($key);
    public function set($key, $value);
}
