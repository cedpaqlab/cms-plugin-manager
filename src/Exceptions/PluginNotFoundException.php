<?php

namespace Cedpaq\PluginManager\Exceptions;

class PluginNotFoundException extends \Exception
{
    public function __construct($type)
    {
        parent::__construct("Plugin type {$type} not found.");
    }
}
