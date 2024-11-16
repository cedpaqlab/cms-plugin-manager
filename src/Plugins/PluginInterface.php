<?php
namespace CMS\PluginSystem\Plugins;

interface PluginInterface {
    public function activate();
    public function deactivate();
}
