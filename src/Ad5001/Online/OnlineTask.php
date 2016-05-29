<?php
namespace Ad5001\Online; 

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat as C;
use pocketmine\IPlayer;
use pocketmine\math\Vector3;

   class OnlineTask extends AsyncTask{
	private $plugin;
	private $sock;
    public function __construct(Plugin $plugin, $sock, $datapath){
        $this->sock = $sock;
        $this->datapath = $datapath;
        $this->isRunning = true;
    }
    public function onRun() {
        $sock = $this->sock;
        socket_listen($sock);
        while ($this->isRunning) {
            $client = socket_accept($sock);
            $input = socket_read($client, 1024);
            $incoming = explode("\r\n", $input);
            $fetchArray = explode(" ", $incoming[0]);
            if($fetchArray[1] == "/"){
                $file = "index.html"; 
                $fetchArray[1] = "index.html"; 
             } else {
                 $filearray = [];
                 $filearray = explode("/", $fetchArray[1]);
                 $file = $fetchArray[1];
             }
             $output = "";
             $Header = "HTTP/1.1 200 OK \r\n" .
             "Date: Fri, 31 Dec 1999 23:59:59 GMT \r\n" .
             "Content-Type: text/html \r\n\r\n";
             $file = ltrim($file, '/');
             if(file_exists($this->datapath . $file)) {
                 $Content = file_get_contents($this->datapath . $file);
             } else {
                 $Content = file_get_contents($this->datapath . "404.html");
             }
             $output = $Header . $Content;
             socket_write($client,$output,strlen($output));
        }
    }
    public function close() {
        $this->isRunning = false;
    }
   }