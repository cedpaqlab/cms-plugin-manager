<?php
namespace Cedpaq\PluginManager\Plugins;

abstract class AbstractPlugin implements PluginInterface {
    protected $config;

    public function __construct($config) {
        $this->config = $config;
    }
}
