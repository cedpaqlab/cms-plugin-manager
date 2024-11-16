<?php
namespace CMS\PluginSystem\Plugins;

use CMS\PluginSystem\Factory\PluginFactory;

class PluginManager {
    private $plugins = [];

    public function loadPlugin($type, $config) {
        $this->plugins[$type] = PluginFactory::create($type, $config);
    }

    public function activatePlugin($type) {
        if (isset($this->plugins[$type])) {
            $this->plugins[$type]->activate();
        }
    }

    public function deactivatePlugin($type) {
        if (isset($this->plugins[$type])) {
            $this->plugins[$type]->deactivate();
        }
    }
}
