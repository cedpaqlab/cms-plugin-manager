<?php
namespace Cedpaq\PluginManager\Plugins;

use Cedpaq\PluginManager\Config\ConfigRepositoryInterface;
use Cedpaq\PluginManager\Factory\PluginFactory;

class PluginManager {
    private $plugins = [];
    private $activePlugins = [];
    private $configRepository;
    private $logPath;

    public function __construct(ConfigRepositoryInterface $configRepository) {
        $this->configRepository = $configRepository;
        $this->logPath = $this->configRepository->get('log_path');
    }

    public function getLogPath() {
        return $this->logPath;
    }

    public function initializePlugins(array $pluginConfigs): void
    {
        // Add or update plugins in the database
        foreach ($pluginConfigs as $name => $config) {
            $this->configRepository->addPlugin($name, [
                'name' => $name,
                'type' => 'plugin',
                'enabled' => $config['enabled'],
                'dependencies' => $config['dependencies'] ?? []
            ]);
        }

        // Load and activate plugins from the database
        $activePlugins = $this->configRepository->getAllActivePlugins();
        foreach ($activePlugins as $plugin) {
            if ($this->allDependenciesActive($plugin['name'], $pluginConfigs)) {
                $this->loadAndActivate($plugin['name'], $plugin);
            }
        }
    }

    private function allDependenciesActive($pluginName, $pluginConfigs): bool {
        if (!empty($pluginConfigs[$pluginName]['dependencies'])) {
            foreach ($pluginConfigs[$pluginName]['dependencies'] as $dependency) {
                if (!$this->configRepository->isPluginActive($dependency)) {
                    error_log("Failed to load $pluginName plugin as $dependency plugin dependency is not active.");
                    return false; // Return false if any dependency is not active
                }
            }
        }
        return true;
    }

    private function loadAndActivate($name, $config): void {
        // Load the plugin if it is not already loaded
        if (!isset($this->plugins[$name])) {
            $this->plugins[$name] = PluginFactory::create($name, $config, $this);
        }

        // Check if the plugin is already activated to avoid repeated activations
        if (!$this->configRepository->isPluginActive($name)) {
            // Resolve dependencies before activating the plugin
            $this->resolveDependencies($name);

            // Activate the plugin after all dependencies are resolved and loaded
            $this->plugins[$name]->activate();
            $this->configRepository->activatePlugin($name);
            $this->logActivation($name);
        }
    }

    // List all loaded plugins
    public function listLoadedPlugins(): string
    {
        if (empty($this->plugins)) {
            return "No plugins are currently loaded.";
        }

        $pluginList = [];
        foreach ($this->plugins as $name => $plugin) {
            // We can add more details here if needed
            $pluginList[] = $name;
        }

        // Return a string with all plugin names, separated by commas
        return implode(", ", $pluginList);
    }

    // Get a plugin
    public function getPlugin($type) {
        if (!isset($this->plugins[$type])) {
            throw new \Exception("Plugin '$type' not found.");
        }
        if (!$this->configRepository->isPluginActive($type)) {
            throw new \Exception("Plugin '$type' not active.");
        }
        return $this->plugins[$type];
    }

    // Deactivate a plugin
    public function deactivatePlugin($type): void
    {
        if (!isset($this->plugins[$type])) {
            error_log("Plugin not loaded: $type");
            throw new \Exception("Plugin '$type' not found.");
        }

        if (!$this->configRepository->isPluginActive($type)) {
            error_log("Plugin not active when trying to deactivate: $type");
            throw new \Exception("Plugin '$type' not active.");
        }

        if ($this->plugins[$type]->deactivate()) {
            $this->configRepository->deactivatePlugin($type);
            unset($this->activePlugins[$type]);
            $this->log("Plugin '$type' has been deactivated.");
        }
    }


    // Method to resolve and activate all dependencies for a given plugin recursively
    private function resolveDependencies($type): void {
        // Retrieve dependencies of the plugin
        $dependencies = $this->plugins[$type]->getDependencies();

        // Ensure that each dependency is loaded and activated
        foreach ($dependencies as $dependency) {
            // Load the dependency if it is not already loaded
            if (!isset($this->plugins[$dependency])) {
                $depConfig = $this->configRepository->get($dependency);
                if ($depConfig && $depConfig['enabled']) {
                    $this->loadAndActivate($dependency, $depConfig);  // Use loadAndActivate to load and activate
                } else {
                    throw new \Exception("Dependency plugin $dependency required by $type is not enabled or not properly configured.");
                }
            } else if (!$this->configRepository->isPluginActive($dependency)) {
                // The dependency is loaded but not activated, so activate it
                $this->plugins[$dependency]->activate();
                $this->configRepository->activatePlugin($dependency);
                $this->logActivation($dependency);
            }
        }
    }

    // Log the activation of a plugin
    private function logActivation($type): void {
        $logPath = $this->getLogPath();
        if (!$logPath) {
            throw new \Exception("Log path is not configured.");
        }
        $this->ensureLogDirectoryExists($logPath);
        file_put_contents($logPath.'plugin_activation.log', "Plugin $type activated at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
    }

    // Ensure the log directory exists before logging
    private function ensureLogDirectoryExists($logPath): void {
        if (!is_dir($logPath)) {
            if (!mkdir($logPath, 0755, true) && !is_dir($logPath)) {
                throw new \Exception("Failed to create log directory: $logPath");
            }
        }
    }

    private function log($message, $level = 'info'): void {
        $logger = $this->getPlugin('Logger');
        if ($logger) {
            $logger->log($message, $level);
        } else {
            error_log("Logger not available: $message");
        }
    }
}
