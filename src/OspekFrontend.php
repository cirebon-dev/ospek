<?php

namespace Ospek;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory ;
class OspekFrontend extends OspekBackend
{
    private $_notif;
    private $_fun;
    private $_joliNotifier;
    
    public function __construct(bool $notif = false)
    {
        $this->pid = getmypid();
        $this->_notif = ($notif)?true:false;
        $this->_joliNotifier = ($notif)?NotifierFactory:: create():false;
    }
    private function _defaultNotif()
    {
        $notification = (new Notification())->setTitle('OSPEK')->setBody("process ".$this->pid." has been stopped!")->setIcon(__DIR__ . '/../asset/icon.png')->addOption('sound', 'Frog');
        $this->_joliNotifier->send($notification);
    }
    public function setNotif(callable $fun)
    {
        $this->_fun = $fun;
    }
        
    public function __destruct()
    {
        if ($this->_notif) {
            if ($this->_fun!=null) {
                ($this->_fun)($this->_joliNotifier, new Notification());
            } else {
                $this->_defaultNotif();
            }
        }
    }
}