<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Cedpaq\PluginManager\Config\AppConfig;
use Cedpaq\PluginManager\Plugins\PluginManager;

$config = AppConfig::getInstance();
$config->set('SEO', ['enabled' => true]);

$pluginManager = new PluginManager();
$pluginManager->loadPlugin('SEO', $config->get('SEO'));
$pluginManager->activatePlugin('SEO');
$pluginManager->deactivatePlugin('SEO');
