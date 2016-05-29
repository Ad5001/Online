<?php
namespace Ad5001\Online ; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
 use pocketmine\Player;
 
 use Ad5001\Online\OnlineTask;


class Main extends PluginBase{
    public function onDisable() {
        $this->socket->close();
        socket_shutdown($this->sock, 2);
    }
public function onEnable(){
// $this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->saveDefaultConfig();
if(!file_exists($this->getDataFolder() . "index.html")) {
    file_put_contents($this->getDataFolder() . "index.html", $this->getResource("index.html"));
}
if(!file_exists($this->getDataFolder() . "404.html")) {
    file_put_contents($this->getDataFolder() . "404.html", $this->getResource("404.html"));
}
set_time_limit(0);

$address = '0.0.0.0';
$port = 8080;

$sock = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
$result = socket_bind($sock, $address, $port) or die('Could not bind to address');

$this->socket = new OnlineTask($this, $sock, $this->getDataFolder());
$this->getServer()->getScheduler()->scheduleAsyncTask($this->socket);
$this->sock = $sock;
}
 public function onCommand(CommandSender $issuer, Command $cmd, $label, array $params){
switch($cmd->getName()){
}
return false;
 }
}