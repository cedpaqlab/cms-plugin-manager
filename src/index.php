<?php
require_once __DIR__ . '/../vendor/autoload.php';

use CMS\PluginSystem\Config\AppConfig;
use CMS\PluginSystem\Plugins\PluginManager;

$config = AppConfig::getInstance();
$config->set('SEO', ['enabled' => true]);

$pluginManager = new PluginManager();
$pluginManager->loadPlugin('SEO', $config->get('SEO'));
$pluginManager->activatePlugin('SEO');
$pluginManager->deactivatePlugin('SEO');
