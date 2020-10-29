<?php

namespace Pantheion\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Manager
{
    protected $files = [
        'DEBUG' => 'logs/debug.log',
        'INFO' => 'logs/info.log',
        'NOTICE' => 'logs/notice.log',
        'WARNING' => 'logs/warning.log',
        'ERROR' => 'logs/error.log',
        'CRITICAL' => 'logs/critical.log',
        'ALERT' => 'logs/alert.log',
        'EMERGENCY' => 'logs/emergency.log',
    ];

    protected $logger;

    public function __construct(string $name = 'zephyr')
    {
        $this->logger = (new Logger($name));
        $this->pushHandlers();
    }

    protected function pushHandlers()
    {
        foreach($this->files as $level => $file) {
            $this->logger->pushHandler(
                new StreamHandler(
                    $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $file, 
                    constant("Monolog\\Logger::$level"),
                    false
                ),
            );
        }
    }

    public function __call(string $name, array $args)
    {
        $levels = array_map(fn($key) => strtolower($key), array_keys($this->files));

        if(!in_array($name, $levels)) {
            throw new \Exception("Invalid log method");
        }

        return $this->logger->$name(...$args);
    }
}