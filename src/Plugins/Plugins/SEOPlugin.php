<?php
namespace Cedpaq\PluginManager\Plugins\Plugins;

use Cedpaq\PluginManager\Plugins\AbstractPlugin;

class SEOPlugin extends AbstractPlugin {

    public function __construct($config, $manager) {
        parent::__construct($config, $manager);
    }

    public function activate() {
        try {
            echo "SEO Plugin activated" . PHP_EOL;
            return true;
        } catch (\Exception $e) {
            echo "Error during SEO activation: " . $e->getMessage() . PHP_EOL;
            return false;
        }
    }

    public function deactivate() {
        try {
            echo "SEO Plugin deactivated" . PHP_EOL;
            return true;
        } catch (\Exception $e) {
            echo "Error during SEO deactivation: " . $e->getMessage() . PHP_EOL;
            return false;
        }
    }

    public function doSomething() {
        $logger = $this->manager->getPlugin('Logger');
        if ($logger) {
            $logger->log("Action performed in SEOPlugin.");
        }
    }
}
