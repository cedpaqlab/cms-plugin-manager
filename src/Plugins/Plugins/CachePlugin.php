<?php
namespace CMS\PluginSystem\Plugins\Plugins;

use CMS\PluginSystem\Plugins\AbstractPlugin;

class CachePlugin extends AbstractPlugin {
    public function activate() {
        echo "Cache Plugin activated";
    }

    public function deactivate() {
        echo "Cache Plugin deactivated";
    }
}
