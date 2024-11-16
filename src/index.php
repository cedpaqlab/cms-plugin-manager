<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Cedpaq\PluginManager\Config\AppConfig;
use Cedpaq\PluginManager\Plugins\PluginManager;
use Cedpaq\PluginManager\Exceptions\PluginNotFoundException;

try {
    $config = AppConfig::getInstance();
    $config->set('log_path', __DIR__ . '/../logs/');
    ini_set('error_log', $config->get('log_path').'errors.log');

    // Plugins config
    $config->set('Logger', ['enabled' => true]);
    $config->set('SEO', ['enabled' => true, 'dependencies' => ['Logger']]);
    $config->set('Cache', ['enabled' => false]);

    $pluginManager = new PluginManager();

    $pluginManager->loadPlugin('Logger', $config->get('Logger'));
    #$pluginManager->activatePlugin('Logger');
    $pluginManager->deactivatePlugin('Logger');

    $pluginManager->loadPlugin('SEO', $config->get('SEO'));
    $pluginManager->activatePlugin('SEO');
    #$pluginManager->deactivatePlugin('SEO');

    #$pluginManager->loadPlugin('Cache1', $config->get('Cache'));
    #$pluginManager->activatePlugin('Cache');
    #$pluginManager->deactivatePlugin('Cache');

    $seoPlugin = $pluginManager->getPlugin('SEO');
    $seoPlugin->doSomething();

    $logger = $pluginManager->getPlugin('Logger');
    if ($logger) {
        $logger->log("Application loaded!");
    }

}
catch (PluginNotFoundException $e)
{
    echo "Plugin error: " . $e->getMessage() . PHP_EOL;
}
catch (\Exception $e)
{
    echo "An unexpected error occurred: " . $e->getMessage() . PHP_EOL;
}