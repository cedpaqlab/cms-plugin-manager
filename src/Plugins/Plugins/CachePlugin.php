<?php
namespace Cedpaq\PluginManager\Plugins\Plugins;

use Cedpaq\PluginManager\Plugins\AbstractPlugin;

class CachePlugin extends AbstractPlugin {
    public function activate() {
        echo "Cache Plugin activated" . PHP_EOL;
    }

    public function deactivate() {
        echo "Cache Plugin deactivated" . PHP_EOL;
    }
}
