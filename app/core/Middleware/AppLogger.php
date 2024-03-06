<?php

namespace ezeasorekene\App\Core\Middleware;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Processor\WebProcessor;

class AppLogger extends Logger
{
    protected $logFilePath;

    public function __construct($name, $log_location = null)
    {
        parent::__construct($name);

        if (empty($log_location)) {
            $log_location = $_ENV['APP_LOG_FILE'] ?? "galaxyphp.log";
        }

        $this->logFilePath = $log_location;

        if (!file_exists($this->logFilePath)) {
            touch($this->logFilePath);
        }

        // Configure the log handlers
        $streamHandler = new StreamHandler($this->logFilePath, Logger::DEBUG);
        $this->pushHandler($streamHandler);

        // Configure the log handler to use the error_log handler
        $errorLogHandler = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::DEBUG);
        $this->pushHandler($errorLogHandler);

        // Add the WebProcessor to include request details
        $webProcessor = new WebProcessor();
        $this->pushProcessor($webProcessor);

        // Check if log file exceeds 5MB
        if ($this->isLogFileExceedsSize()) {
            $this->archiveLogFile();
        }

    }

    protected function isLogFileExceedsSize(int $size = 5)
    {
        $fileSize = filesize($this->logFilePath);
        return $fileSize >= $size * 1024 * 1024; // 5MB in bytes
    }

    protected function archiveLogFile()
    {
        $archiveLogFilePath = $this->logFilePath . '.' . date('Y-m-d_H-i-s');

        rename($this->logFilePath, $archiveLogFilePath);

        $fileHandler = new StreamHandler($this->logFilePath, Logger::DEBUG);
        $this->popHandler(); // Remove the old file handler
        $this->pushHandler($fileHandler); // Add the new file handler
    }

    public function logInfo($message, $data = [])
    {
        $this->info($message, $data);
    }

    public function logWarning($message, $data = [])
    {
        $this->warning($message, $data);
    }

    public function logDebug($message, $data = [])
    {
        $this->debug($message, $data);
    }

    public function logError($message, $data = [])
    {
        $this->error($message, $data);
    }


}