<?php
namespace Cedpaq\PluginManager\Plugins\Plugins;

use Cedpaq\PluginManager\Plugins\AbstractPlugin;

class CachePlugin extends AbstractPlugin {
    public function activate() {
        try {
            echo "Cache Plugin activated" . PHP_EOL;
            return true;
        } catch (\Exception $e) {
            echo "Error during Cache activation: " . $e->getMessage() . PHP_EOL;
            return false;
        }
    }

    public function deactivate() {
        try {
            echo "Cache Plugin deactivated" . PHP_EOL;
            return true;
        } catch (\Exception $e) {
            echo "Error during Cache deactivation: " . $e->getMessage() . PHP_EOL;
            return false;
        }
    }
}
