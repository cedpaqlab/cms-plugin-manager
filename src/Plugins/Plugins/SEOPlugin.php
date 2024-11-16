<?php
namespace CMS\PluginSystem\Plugins\Plugins;

use CMS\PluginSystem\Plugins\AbstractPlugin;

class SEOPlugin extends AbstractPlugin {
    public function activate() {
        echo "SEO Plugin activated";
    }

    public function deactivate() {
        echo "SEO Plugin deactivated";
    }
}
