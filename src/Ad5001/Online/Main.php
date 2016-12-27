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

    protected $handlers;
    protected $arguments;


    public function onEnable(){
        $this->saveDefaultConfig();
        $this->handlers = [];
        $this->arguments = [];


        $this->getServer()->getScheduler()->scheduleRepeatingTask($task = new ArgFillTask($this), 20*10);
        @mkdir($this->getDataFolder() . "tmp");
        $task->onRun(0);

        if(!file_exists($this->getDataFolder() . "tmp/router.php")) {
            file_put_contents($this->getDataFolder() . "tmp/router.php", $this->getResource("handler.php"));
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


        set_time_limit(0);

        $this->port = $this->getConfig()->get("port");

        if(!UPnP::PortForward($this->port)) {// Beta for Windows
            $this->getLogger()->info("Not able to port forward!");
        }

        $this->getServer()->getScheduler()->scheduleAsyncTask(new execTask($this->getServer()->getFilePath()));
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


    /*
    Adds a PHP handler
    @param     $path    string
    @return bool
    */
    public function addHandler(string $path) : bool {
        if(file_exists($path)) {
            $this->handlers[$path] = $path;
            return true;
        }
        return false;
    }


    /*
    Return an array with all handlers
    @return array
    */
    public function getHandlers() : array {
        return $this->handlers;
    }


    /*
    Sets a plugin argument
    @param     $plugin   \pocketmine\plugin\Plugin
    @param     $id          string
    @param     $value    mixed
    @return bool
    */
    public function setArgument(\pocketmine\plugin\Plugin $plugin, string $id, $value) : bool {
        if(!isset($this->arguments[$plugin->getName()])) $this->arguments[$plugin->getName()] = [];
        $this->arguments[$plugin->getName()][$id] = $value;
        return true;
    }


    /*
    Gets a plugin arguments
    @param     $plugin    \pocketmine\plugin\Plugin
    @return array
    */
    public function getArguments(\pocketmine\plugin\Plugin $plugin) : array {
        return isset($this->arguments[$plugin->getName()]) ? $this->arguments[$plugin->getName()] : [];
    }



}




/**
 * Setting rguments for the pocketine infos
 */
class ArgFillTask extends \pocketmine\scheduler\PluginTask {
    
    /*
    Constructs the class
    @param     $main      Main
    */
    public function __construct(Main $main) {
        parent::__construct($main);
        $this->main = $main;
        $this->server = $main->getServer();
    }

    /*
    Called when the task runs
    @param     $tick    mixed
    */
    public function onRun($tick) {
        $server = $this->server;
        $pmargs = [];
        $pluginsargs = [];
        $players = [];
        $levels = [];

        // Filling the arguments

        // 1. Pocketmine
        $pmargs["software_name"] = $server->getName();
        $pmargs["software_codename"] = $server->getCodename();
        $pmargs["software_version"] = $server->getPocketmineVersion();
        $pmargs["mcpe_version"] = $server->getVersion();
        $pmargs["API"] = $server->getApiVersion();
        $pmargs["software_datapath"] = $server->getDataPath();
        $pmargs["software_pluginpath"] = $server->getPluginPath();
        $pmargs["max_players"] = $server->getMaxPlayers();
        $pmargs["port"] = $server->getPort();
        $pmargs["view_distance"] = $server->getViewDistance();
        $pmargs["ip"] = $server->getIp();
        $pmargs["server_unique_id"] = $server->getServerUniqueId();
        $pmargs["auto_save"] = $server->getAutoSave();
        $pmargs["default_gamemode"] = $server->getGamemode();
        $pmargs["force_gamemode"] = $server->getForceGamemode();
        $pmargs["difficulty"] = $server->getDifficulty();
        $pmargs["spawn_radius"] = $server->getSpawnRadius();
        $pmargs["allow_flight"] = $server->getAllowFlight();
        $pmargs["ultra_hardcore"] = $server->isHardcore();
        $pmargs["motd"] = $server->getMotd();
        $pmargs["tps"] = $server->getTicksPerSecond();
        $pmargs["lang_name"] = $server->getLanguage()->getName();
        $pmargs["lang_code"] = $server->getLanguage()->getLang();

        // 2. Plugins
        foreach($server->getPluginManager()->getPlugins() as $plugin) {
            $pl = [];
            $pl["name"] = $plugin->getName();
            $pl["is_enabled"] = $plugin->isEnabled();
            $pl["data_folder"] = $plugin->getDataFolder();
            $pl["apis"] = $plugin->getDescription()->getCompatibleApis();
            $pl["authors"] = $plugin->getDescription()->getAuthors();
            $pl["prefix"] = $plugin->getDescription()->getPrefix();
            $pl["commands"] = $plugin->getDescription()->getCommands();
            $pl["dependencies"] = $plugin->getDescription()->getDepend();
            $pl["description"] = $plugin->getDescription()->getDescription();
            $pl["load_before"] = $plugin->getDescription()->getLoadBefore();
            $pl["main"] = $plugin->getDescription()->getMain();
            $pl["order"] = $plugin->getDescription()->getOrder();
            $pl["soft_depend"] = $plugin->getDescription()->getSoftDepend();
            $pl["version"] = $plugin->getDescription()->getVersion();
            $pl["website"] = $plugin->getDescription()->getWebsite();
            foreach($this->main->getArguments($plugin) as $name => $arg) {
                if(!isset($pl[$name])) {
                    $pl[$name] = $arg;
                }
            }
            $pluginsargs[$plugin->getName()] = $pl;
        }


        // 3. Players
        foreach ($server->getOnlinePlayers() as $player) {
            $pl = [];
            $pl["client_secret"] = $player->getClientSecret();
            $pl["banned"] = $player->isBanned(); // I don't know if tis could be to false but we never know xD
            $pl["whitelisted"] = $player->isWhitelisted();
            $pl["client_secret"] = $player->getClientSecret();
            $pl["first_played"] = $player->getFirstPlayed();
            $pl["last_played"] = $player->getLastPlayed();
            $pl["played_before"] = $player->hasPlayedBefore();
            $pl["allow_flight"] = $player->getAllowFlight();
            $pl["flying"] = $player->isFlying();
            $pl["auto_jump"] = $player->hasAutoJump();
            $pl["op"] = $player->isOp();
            $pl["connected"] = $player->isConnected();
            $pl["display_name"] = $player->getDisplayName();
            $pl["ip"] = $player->getAddress();
            $pl["port"] = $player->getPort();
            $pl["is_sleeping"] = $player->isSleeping();
            $pl["ticks_in_air"] = $player->getInAirTicks();
            $pl["gamemode"] = $player->getGamemode();
            $pl["name"] = $player->getName();
            $pl["loader_id"] = $player->getLoaderId();
            $players[$player->getName()] = $pl;
        }


        // 4.Levels
        foreach ($server->getLevels() as $level) {
            $lvl = [];
            $lvl["tick_rate"] = $level->getTickRate();
            $lvl["id"] = $level->getId();
            $lvl["auto_save"] = $level->getAutoSave();
            $lvl["tick_rate"] = $level->getTickRate();
            $lvl["players"] = [];
            foreach ($level->getPlayers() as $pl) {
                $lvl["players"][$pl->getName()] = $players[$pl->getName()];
            }
            $lvl["time"] = $level->getTime();
            $lvl["name"] = $level->getName();
            $lvl["folder_name"] = $level->getFolderName();
            $lvl["seed"] = $level->getSeed();
            $levels[$lvl["name"]] = $lvl;
        }

        file_put_contents($this->main->getDataFolder() . "tmp/args", json_encode(["POCKETMINE" => $pmargs, "PLUGINS" => $pluginsargs, "PLAYERS" => $players, "LEVELS" => $levels, "HANDLERS" => $this->main->getHandlers()]));
    }
}







class execTask extends \pocketmine\scheduler\AsyncTask {

    public function __construct(string $path) {
        $this->path = $path;
    }

    public function onRun() {
        $address = '0.0.0.0';
        $port = yaml_parse(file_get_contents("plugins\\Online\\config.yml"))["port"];
        switch(true) {
            case stristr(PHP_OS, "WIN"):
            // echo '"%CD%\\bin\\php\\php.exe -t %CD%\\plugins\\Online -n -d include_path=\'%CD%\\plugins\\Online\\\' -S ' . $address . ":" . $port . ' -f %CD%\\plugins\\Online\\router.php"';
            shell_exec('start /MIN cmd /c "%CD%\\bin\\php\\php.exe -t %CD%\\plugins\\Online -n -d include_path=\'%CD%\\plugins\\Online\\\' -d extension=\'%CD%\\bin\\php\\ext\\php_yaml.dll\' -S ' . $address . ":" . $port . ' tmp/router.php"');
            break;
            case stristr(PHP_OS, "DAR"):
            shell_exec('open -a Terminal php  -t ' . $this->path . "plugins\\Online -n -d include_path=\'" . $this->path . "plugins\\Online\\\' -d extension=\'" . $this->path . "bin\\php\\ext\\php_yaml.dll\' -S " . $address . ":" . $port . ' tmp/router.php"');
            break;
            case stristr(PHP_OS, "LINUX"):
            shell_exec('gnome-terminal -e php -t ' . $this->path . "plugins\\Online -n -d include_path=\'" . $this->path . "plugins\\Online\\\' -d extension=\'" . $this->path . "bin\\php\\ext\\php_yaml.dll\' -S " . $address . ":" . $port . ' tmp/router.php"');
            break;
        }
    }
} 