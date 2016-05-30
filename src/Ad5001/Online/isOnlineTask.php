<?php
namespace Ad5001\Online; 

use pocketmine\Server;
use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\Task;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ServerScheduler;

use Ad5001\Online\OnlineTask;

   class isOnlineTask extends PluginTask{
	private $plugin;
	private $sock;
    public function __construct(Plugin $plugin, $sock, $datapath){
        parent::__construct($plugin);
        $this->pl = $plugin;
        $this->sock = $sock;
        $this->datapath = $datapath;
        $this->isRunning = true;
    }
    public function close() {
        $this->isRunning = false;
    }
    public function onRun($tick) {
        if($this->isRunning) {
            socket_listen($this->sock);
            $this->pl->getServer()->getScheduler()->scheduleAsyncTask(new OnlineTask($this->pl, $this->sock, $this->datapath));
        }
    }
   }