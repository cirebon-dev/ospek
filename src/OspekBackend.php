<?php
namespace Ospek;
class OspekBackend
{
    protected $pid;
    private $_pidFile;
    private $_command;
    private $_logFile;

    public function __construct(String $cl=null, String $log=null) 
    {
        if ($cl != null) {
            $this->_command = $cl;
            if ($log != null) {
                $this->_logFile = $log;
            } else {
                $this->_logFile = defined('PHP_WINDOWS_VERSION_BUILD')?"NUL":"/dev/null";
            }
            $this->_run();
        }
    }
    private function _run() 
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $command = $this->_command.' > '.$this->_logFile;
            exec("start /B ".$command." 2>&1");
        } else {
                 
            $command = $this->_command.' > '.$this->_logFile.' 2>&1 & echo $!';
            exec($command, $op);
            $this->pid = (int)$op[0];
        }
    }

    public function setPid(int $pid) 
    {
        $this->pid = $pid;
    }

    public function getPid():int 
    {
        return $this->pid;
    }

    public function status():bool 
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $process = shell_exec(sprintf('tasklist.exe /FI "PID eq %d" /FO CSV /NH', $this->pid));

            return in_array($this->pid, str_getcsv($process, ','));
        }
        
        return (bool) posix_kill($this->pid, 0);
    }

    public function start() 
    {
        if ($this->_command !=null) {
            $this->_run();
        }
        throw new \LengthException("command is empty!");
    }

    public function stop():bool 
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            shell_exec(sprintf('taskkill.exe /PID %d', $this->pid));
            return true;
      
        } else {

            posix_kill($this->pid, SIGTERM);
            return true;
        }
    }
    public function getOutput(String $file=null):Array 
    {
        $file = ($file)?$file:$this->_logFile;
        if (file_exists($file)) {
            return file($file);
        }
        throw new \RunTimeException("file not found!");
    }
}