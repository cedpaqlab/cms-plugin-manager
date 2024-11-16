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
            // Simuler une erreur pour démonstration
            if (false) {
                throw new \Exception("Failed to activate SEO Plugin.");
            }
        } catch (\Exception $e) {
            echo "Error during activation: " . $e->getMessage() . PHP_EOL;
        }
    }

    public function deactivate() {
        try {
            echo "SEO Plugin deactivated" . PHP_EOL;
        } catch (\Exception $e) {
            echo "Error during deactivation: " . $e->getMessage() . PHP_EOL;
        }
    }

    public function getDependencies() {
        return $this->config['dependencies'] ?? [];
    }

    public function doSomething() {
        $logger = $this->manager->getPlugin('Logger');
        if ($logger) {
            $logger->log("Action performed in SEOPlugin.");
        } else {
            echo "Logger not available.";
        }
    }
}
