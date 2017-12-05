<?php

namespace Wilderborn\Partyline;

use Illuminate\Console\Command;
use Log;

class Partyline
{
    /**
     * @var Command|NullConsole
     */
    private $console;

    /**
     * @var bool Send lines to Laravel's log
     */
    private $log = false;

    /**
     * Set the console command instance
     *
     * @param Command $console
     * @return void
     */
    public function bind(Command $console)
    {
        $this->console = $console;
    }

    /**
     * Pass any method calls onto the console instance
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($this->log) {
            $this->sendToLog($name, $arguments[0]);
        }

        $console = $this->console ?: new NullConsole;

        return call_user_func_array([$console, $name], $arguments);
    }

    /**
     * Enables or disables logging all the lines to Laravel's Log
     * @param bool $log
     */
    public function setEnableLog(bool $log)
    {
        $this->log = $log;
    }

    /**
     * Is log enabled?
     * @return bool
     */
    public function getEnableLog()
    {
        return $this->log;
    }

    /**
     * Logs $string to Laravel's Log, mapping the console function being called
     * to a suitable log level
     * @param  string $name   Command function called
     * @param  string $string Text to log
     * @return void
     */
    public function sendToLog($name, $string)
    {
        switch ($name) {
            case 'info':
                Log::info($string);
                break;
            case 'error':
                Log::error($string);
                break;
            case 'warn':
                Log::warning($string);
                break;
            case 'alert':
                Log::notice($string);
                break;
            case 'question':
            case 'comment':
            case 'line':
            default:
                Log::debug($string);
        }
    }
}
