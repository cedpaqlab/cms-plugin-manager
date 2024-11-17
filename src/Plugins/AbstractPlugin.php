<?php
namespace Cedpaq\PluginManager\Plugins;

abstract class AbstractPlugin implements PluginInterface {
    protected $config;
    protected $manager;

    public function __construct($config, $manager) {
        $this->config = $config;
        $this->manager = $manager;
    }

    public function getDependencies() {
        return $this->config['dependencies'] ?? [];
    }

    abstract public function activate();
    abstract public function deactivate();
}
