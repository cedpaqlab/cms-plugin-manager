<?php
namespace Cedpaq\PluginManager\Plugins\Plugins;

use Cedpaq\PluginManager\Plugins\AbstractPlugin;

class SEOPlugin extends AbstractPlugin {
    public function activate() {
        echo "SEO Plugin activated";
    }

    public function deactivate() {
        echo "SEO Plugin deactivated";
    }
}
