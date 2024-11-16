<?php
namespace Cedpaq\PluginManager\Plugins;

interface PluginInterface {
    public function activate();
    public function deactivate();
}
