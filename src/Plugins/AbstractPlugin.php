<?php
namespace Cedpaq\PluginManager\Plugins;

abstract class AbstractPlugin implements PluginInterface {
    protected $config;

    public function __construct($config, $manager) {
        $this->config = $config;
        $this->manager = $manager;
    }

    public function getConfig() {
        return $this->config;
    }

    public function __toString() {
        return static::class;
    }

    abstract public function activate();
    abstract public function deactivate();
}
