<?php
namespace Cedpaq\PluginManager\Plugins;

use Cedpaq\PluginManager\Config\AppConfig;
use Cedpaq\PluginManager\Factory\PluginFactory;

class PluginManager {
    private $plugins = [];
    private $activePlugins = [];
    private $logPath; // Global logs path

    public function __construct() {
        $this->logPath = AppConfig::getInstance()->get('log_path');
    }
    public function getLogPath() {
        return $this->logPath; // Getter
    }

    // Get a plugin
    public function getPlugin($type) {
        if (!isset($this->plugins[$type])) {
            throw new \Exception("Plugin '$type' not found.");
        }
        if (!$this->isActive($type)){
            throw new \Exception("Plugin '$type' not active.");
        }
        return $this->plugins[$type];
    }

    // Load a plugin if it is enabled, without activating it
    public function loadPlugin($type, $config) {
        // Is plugin enabled
        if (!isset($config['enabled']) || !$config['enabled']) {
            throw new \Exception("Plugin $type is not enabled and cannot be loaded.");
        }
        // Load only if not already loaded
        if (!isset($this->plugins[$type])) {
            $this->plugins[$type] = PluginFactory::create($type, $config, $this);
        } else {
            $this->log("Plugin already loaded", "warning");
        }
    }

    // Activate a plugin, ensuring all its dependencies are loaded and activated first
    public function activatePlugin($type) {
        if (!isset($this->plugins[$type])) {
            throw new \Exception("Plugin $type not loaded.");
        }
        if (!in_array($type, $this->activePlugins)) {
            $this->resolveDependencies($type);
            $this->plugins[$type]->activate();
            $this->activePlugins[] = $type;
            $this->logActivation($type);
        }
    }

    // Resolve and activate all dependencies for a given plugin recursively
    private function resolveDependencies($type): void
    {
        $dependencies = $this->plugins[$type]->getDependencies();
        foreach ($dependencies as $dependency) {
            if (!isset($this->plugins[$dependency])) {
                $depConfig = AppConfig::getInstance()->get($dependency);
                if ($depConfig && $depConfig['enabled']) {
                    $this->loadPlugin($dependency, $depConfig);
                    $this->activatePlugin($dependency);
                } else {
                    throw new \Exception("Dependency plugin $dependency required by $type is not enabled or not properly configured.");
                    continue;
                }
            } else if (!$this->isActive($dependency)) {
                // Already loaded but not activated
                $this->activatePlugin($dependency);
            }
        }
    }

    public function deactivatePlugin($type): void
    {
        if (isset($this->plugins[$type])) {
            // Check if the plugin is currently active before attempting to deactivate it
            if ($this->isActive($type)) {
                if ($this->plugins[$type]->deactivate()) {
                    $this->activePlugins = array_diff($this->activePlugins, [$type]);
                    $this->log("Plugin '$type' has been deactivated.");
                }
            } else {
                throw new \Exception("Attempted to deactivate an inactive plugin: '$type'.");
            }
        } else {
            throw new \Exception("Attempted to deactivate a non-existent plugin: '$type'.");
        }
    }

    /**
     * Check if a plugin is active.
     * This method needs to accurately reflect how you track the active state of plugins.
     */
    private function isActive($type): bool
    {
        return in_array($type, $this->activePlugins);
    }

    // Log the activation of a plugin
    private function logActivation($type): void
    {
        $logPath = AppConfig::getInstance()->get('log_path');
        if (!$logPath) {
            throw new \Exception("Log path is not configured.");
        }
        // Ensure the log directory exists
        $this->ensureLogDirectoryExists($logPath);
        file_put_contents($logPath.'plugin_activation.log', "Plugin $type activated at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
    }

    // Ensure the log directory exists before logging
    private function ensureLogDirectoryExists(): void
    {
        $logDir = $this->logPath;
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                throw new \Exception("Failed to create log directory: $logDir");

            }
        }
    }

    private function log($message, $level = 'info'): void
    {
        $logger = $this->getPlugin('Logger');
        if ($logger) {
            $logger->log($message, $level);
        } else {
            error_log("Logger not available: $message");
        }
    }
}
