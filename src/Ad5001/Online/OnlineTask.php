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
use pocketmine\utils\Config;
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
        $this->cfg = new Config($datapath . "config.yml", Config::YAML);
    }
    public function close() {
        $this->isRunning = false;
    }
    public function onRun() {
            socket_listen($this->sock);
        $sock = $this->sock;
            $client = socket_accept($sock);
            $input = socket_read($client, 1024);
            $incoming = explode("\r\n", $input);
            $fetchArray = explode(" ", $incoming[0]);
            if($fetchArray[1] == "/"){
                $file =  $this->cfg->get("index"); 
                $fetchArray[1] =  $this->cfg->get("index");
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
             if(strpos($file, "?")) {
                 $exe = explode("?", $file);
                 $file = $exe[0];
                 $exe = explode("&", $exe[1]);
             }
             
             if(file_exists($this->datapath . $file)) {
                 if(pathinfo($this->datapath . $file)['extension'] === "php") {
                     if(isset($exe[0])) {
                         $GET = [];
                         foreach($exe as $exes) {
                             $ex = explode("=", $exes);
                             array_push($GET, "\"{$ex[0]}\" => \"{$ex[1]}\"");
                         }
                         $current = '<?php 
$GET = [' . implode("," . PHP_EOL, $GET) . '];
?>' . file_get_contents($this->datapath . $file);
                         $current = str_ireplace('$_GET', '$GET', $current);
                         file_put_contents($this->datapath . "current.php", $current);
                         $file = "current.php";
                     }
                     ob_start();
                     include $this->datapath . $file ;
                     $Content = ob_get_contents();
                     ob_end_clean();
                 } else {
                     $Content = file_get_contents($this->datapath . $file);
                 }
             $Header = "HTTP/1.1 200 OK \r\n" .
"Date: Fri, 31 Dec 1999 23:59:59 GMT \r\n" .
"Content-Type: text/html \r\n\r\n";
             } else {
             $Header = "HTTP/1.1 404 NOT FOUND \r\n" .
"Date: Fri, 31 Dec 1999 23:59:59 GMT \r\n" .
"Content-Type: text/html \r\n\r\n";
                 $Content = file_get_contents($this->datapath . $this->cfg->get("404"));
             }
             foreach($this->cfg->get("denied-pages") as $dp) {
                 if($dp === $file) {
                     $Header = "HTTP/1.1 403 FORBIDDEN \r\n" .
"Date: Fri, 31 Dec 1999 23:59:59 GMT \r\n" .
"Content-Type: text/html \r\n\r\n";
                     $Content = file_get_contents($this->datapath . $this->cfg->get("403"));
                 }
             }
             $output = $Header . $Content;
             socket_write($client,$output,strlen($output));
    }
   }