<?php
namespace Cedpaq\PluginManager\Plugins\Plugins;

use Cedpaq\PluginManager\Plugins\AbstractPlugin;

class LoggerPlugin extends AbstractPlugin {
    public function activate() {
        echo "Logger Plugin activated" . PHP_EOL;
    }

    public function deactivate() {
        echo "Logger Plugin deactivated" . PHP_EOL;
    }

    public function getDependencies() {
        return [];
    }

    public function log($message) {
        if (!$this->manager) {
            throw new \Exception("Plugin manager not set for LoggerPlugin.");
        }
        $logPath = $this->manager->getLogPath();
        $logFile = $logPath . 'logger.log';

        $timestamp = date('Y-m-d H:i:s');
        $callerClass = $this->getCallerClassName();

        $logMessage = "$timestamp: ".(!empty($callerClass) ? '['.$callerClass.'] ' : '')."$message" . PHP_EOL;

        if (!file_exists($logFile)) {
            file_put_contents($logFile, "", FILE_APPEND);
        }
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    private function getCallerClassName() {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3); // Limit for performances
        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && $trace['class'] !== __CLASS__) {
                return basename(str_replace('\\', '/', $trace['class'])); // Extraction class
            }
        }
        return '';
    }
}
