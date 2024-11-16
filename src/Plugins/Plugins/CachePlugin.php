<?php
namespace Cedpaq\PluginManager\Plugins\Plugins;

use Cedpaq\PluginManager\Plugins\AbstractPlugin;

class CachePlugin extends AbstractPlugin {
    public function activate() {
        echo "Cache Plugin activated";
    }

    public function deactivate() {
        echo "Cache Plugin deactivated";
    }
}
