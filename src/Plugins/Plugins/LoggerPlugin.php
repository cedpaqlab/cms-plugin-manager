<?php
namespace Cedpaq\PluginManager\Plugins\Plugins;

use Cedpaq\PluginManager\Plugins\AbstractPlugin;
use DateTime;
use DateTimeZone;

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

    public function log($message, $level = 'info') {
        $level = strtoupper(substr($level, 0, 4));
        if (!$this->manager) {
            throw new \Exception("Plugin manager not set for LoggerPlugin.");
        }
        $logPath = $this->manager->getLogPath();
        $logFile = $logPath . 'logger.log';

        // Format the timestamp in UTC
        $timestamp = new DateTime('now', new DateTimeZone('UTC'));
        $formattedTimestamp = $timestamp->format('d-M-Y H:i:s') . ' UTC'; // Adjusted format to match your example

        $callerClass = $this->getCallerClassName();

        // Build the log message with the desired format
        $logMessage = "[$level][$formattedTimestamp]".(!empty($callerClass) ? "[$callerClass] " : ' ')."$message" . PHP_EOL;

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
