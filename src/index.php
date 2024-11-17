<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Cedpaq\PluginManager\Config\AppConfig;
use Cedpaq\PluginManager\Plugins\PluginManager;
use Cedpaq\PluginManager\Exceptions\PluginNotFoundException;
use Cedpaq\PluginManager\Config\MongoConfigRepository;

try
{
    // Initialize configuration repository
    $configRepository = new MongoConfigRepository();
    $config = AppConfig::getInstance($configRepository);

    // Setup logging path
    $config->set('log_path', __DIR__ . '/../logs/');
    ini_set('error_log', $config->get('log_path') . 'errors.log');

    // Initialize plugin manager
    $pluginManager = new PluginManager($configRepository);

    // Define plugin configurations
    $pluginConfigs = [
        'Logger' => ['enabled' => true],
        'Cache' => ['enabled' => true],
        'SEO' => ['enabled' => true, 'dependencies' => ['Logger']],
        'CMS' => ['enabled' => true, 'dependencies' => ['SEO', 'Cache']]
    ];

    // Load and conditionally activate plugins based on configuration and database state
    $pluginManager->initializePlugins($pluginConfigs);

    echo "DEBUG loaded plugins: ".$pluginManager->listLoadedPlugins() . PHP_EOL;

    // Usages
    #$seoPlugin = $pluginManager->getPlugin('SEO');
    #$seoPlugin->doSomething();

}
catch (PluginNotFoundException $e)
{
    echo "Plugin error: " . $e->getMessage() . PHP_EOL;
    error_log("Plugin error: " . $e->getMessage());
}
catch (\Exception $e)
{
    echo "An unexpected error occurred: " . $e->getMessage() . PHP_EOL;
    error_log("An unexpected error occurred: " . $e->getMessage());
}