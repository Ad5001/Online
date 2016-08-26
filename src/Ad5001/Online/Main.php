<?php
namespace Ad5001\Online; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\network\upnp\UPnP;
use pocketmine\Server;
use pocketmine\Player;


class Main extends PluginBase{
public function onEnable(){
$this->saveDefaultConfig();

if(!stream_resolve_include_path("router.php")) {
    file_put_contents($this->getDataFolder() . "router.php", $this->getResource("handler.php"));
}
foreach($this->getConfig()->get("Domains") as $d) {
    @mkdir($this->getDataFolder() . $d);
    if(!file_exists($this->getDataFolder() . $d . "/index.html") and !file_exists($this->getDataFolder() . $d . "/index.php")) {
        file_put_contents($this->getDataFolder() .$d. "/index.html", $this->getResource("index.html"));
    }
    if(!file_exists($this->getDataFolder() .$d. "/404.html")) {
        file_put_contents($this->getDataFolder() . $d . "/404.html", $this->getResource("404.html"));
    }
    if(!file_exists($this->getDataFolder() . $d . "/403.html")) {
        file_put_contents($this->getDataFolder() .$d . "/403.html", $this->getResource("403.html"));
    }
}

register_shutdown_function("Ad5001\\Online\\Main::shutdown");

set_time_limit(0);

$this->port = $this->getConfig()->get("port");

if(!UPnP::PortForward($this->port)) {// Beta for Windows
    $this->getLogger()->info("Not able to port forward!");
}

$this->getServer()->getScheduler()->scheduleAsyncTask(new execTask($this->getServer()->getFilePath()));
}

public static function shutdown() {
    echo "Shutdowned !";
}

public function onDisable() {
    if($this->getConfig()->get("KillOnShutdown") !== "false") {
        $this->getLogger()->info("Shutdowning.....");
        switch(true) {
            case stristr(PHP_OS, "WIN"):
            shell_exec('FOR /F "tokens=5" %P IN (\'netstat -a -n -o ^| findstr 0.0.0.0:'. $this->port .'\') DO TaskKill.exe /F /PID %P');
            $this->getLogger()->info("Shutdowned on Windows !");
            break;
            case stristr(PHP_OS, "DAR") or stristr(PHP_OS, "LINUX"):
            shell_exec("kill -kill `lsof -t -i tcp:$this->port`");
            $this->getLogger()->info("Shutdowned on Linux or MAC !");
            break;
        }
    }
    
}
}

class execTask extends \pocketmine\scheduler\AsyncTask {

    public function __construct(string $path) {
        $this->path = $path;
    }

    public function onRun() {
        $address = '0.0.0.0';
        $port = yaml_parse(file_get_contents("plugins\\Online\\config.yml"))["port"];
        // shell_exec("cd plugins/Online");
        switch(true) {
            case stristr(PHP_OS, "WIN"):
            // echo '"%CD%\\bin\\php\\php.exe -t %CD%\\plugins\\Online -n -d include_path=\'%CD%\\plugins\\Online\\\' -S ' . $address . ":" . $port . ' -f %CD%\\plugins\\Online\\router.php"';
            shell_exec('start "Online Listener" cmd /c "%CD%\\bin\\php\\php.exe -t %CD%\\plugins\\Online -n -d include_path=\'%CD%\\plugins\\Online\\\' -d extension=\'%CD%\\bin\\php\\ext\\php_yaml.dll\' -S ' . $address . ":" . $port . ' router.php"');
            break;
            case stristr(PHP_OS, "DAR"):
            shell_exec('open -a Terminal "' . $this->path . "bin\\php\\php.exe -t " . $this->path . "plugins\\Online -n -d include_path=\'" . $this->path . "plugins\\Online\\\' -d extension=\'" . $this->path . "bin\\php\\ext\\php_yaml.dll\' -S " . $address . ":" . $port . ' router.php"');
            break;
            case stristr(PHP_OS, "LINUX"):
            shell_exec('gnome-terminal -e "' . $this->path . "bin\\php\\php.exe -t " . $this->path . "plugins\\Online -n -d include_path=\'" . $this->path . "plugins\\Online\\\' -d extension=\'" . $this->path . "bin\\php\\ext\\php_yaml.dll\' -S " . $address . ":" . $port . ' router.php"');
            break;
        }
    }
}