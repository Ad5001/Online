## This repository was moved to [git.ad5001.eu](https://git.ad5001.eu/Ad5001/Online)
# Online
_______
Create multiple website for your PocketMine server fully free !

This plugin is under the [BoxOfDevs LICENSE v1.0](blob/master/LICENSE)

### Create your first website
<!--
By default , the plugin generates a index.html (change it !), a 404.html and a 403.html .
Use the plugin folder as website root folder.

In the config, you can change: index page, 404 page, 403 page, the website port, and thz forbidden acess page.
-->
#### WHEN UPDATING TO 1.5, REMEMBER TO DELETE YOUR CONFIG! A NEW ONE WILL BE GENERATED WITH NEW VALUES !
To create a website, go to the config.yml -> plugins/Online/config.yml    
Then add a new website in the "Domains" array (by default, there is localhost) with your website name so it looks like this (for this whole example, we will use example.com but local servers are already here by default. It's if you want to add a new one):

```yaml
Domains:
   - localhost
   - example.com
```

Restart your server and a new folder in the Online folder will be generated! It will be your new website path.

By default, there will be an index.html, a 404.html and a 403.html.

You can modify them, add more files, ect....     

If you're using an external domain, portforward your machine (later on the lesson) , then go to your registar CPanel and go to records -> add new record and then put it to CName to your portforwarded machine. All of this isn't required localy.

After have done this, connect yourself to the website by your browser: http://example.com:Your port (by default 80).

To make everyone able to see your website, you need to port forward your website (if you have port forwared your server, you will also need to do this) . Note that the port forward should be automatized on windows devices using UPnP.

There are tons of tutorials on how to portforward something (google it !) but there are some specific config to do.

And there you go ! You've created your first website with online ! Good job !

Config of the port foreward:   
Name: HTTP   
Start port: Your port (by default 80)   
End port: Your port (by default 80)   
Protocol: TPC     

Start your server and profit of your new website !

### Add a parked domain

You've created a website but you want to broadcast it on multiple websites? No problem ! Look at the tutorial below and see how to do this !     

1. You need first to have done [you're first website](#Create-your-first-website)
2. Go to your config (still the same and add a new element: `parked domain: current domain` (there is by default 0.0.0.0 and 127.0.0.1 that points to localhost) So it looks like this (for this example, example.net is the parked domain and example.com is the existing one):
```yaml
Parked domains:
  0.0.0.0: localhost
  127.0.0.1: localhost
  example.net: example.com
```
3. Then, go to your parked domain registar CPanel and add a new CName record pointing to the machine IP.
4. There you go, you created a parked domain !

### Tips & tricks:
You might have seen that the tutorials above haven't covered the whole configs and stuff !
Here are some tips & tricks that what are they here for !

1. <u>Changing port</u>: Yes, it's not always easy to use port 80 ! It's often binded... CHange the value of "port" in the config and set it to your new port and it will work fine !
2. <u>Errors and index</u>: Change index, 404 and 403 to customize even more you're website ! Change them to customisze even more your website ! 404 is when a page is not found, 403 is when the user is not allowed to view this page. You can change them to make it even more customisable !
3. <u>Denied pages</u>: Sometimes, you don't want the user to see some pages so add them to the denied page array to block them from being viewed ! Example: 
```yaml
denied-pages:
   - website/path/to/file
```
4. <u>Set the server still open even if the server's stop</u>: Set the KillOnShutdown to false. What does that mean? Simply that the websites and domains will still remain even if the server is offline. BUT remember that those issues can happend: Server got stuck on disable: Problem at starting. Unable to stop it on hoster. So DON'T MODIFY IT IF YOU'RE NOT USING A VPS OR LOCALLY. Command to stop it :    
Windows:
```batch
FOR /F "tokens=5" %P IN (\'netstat -a -n -o ^| findstr 0.0.0.0:<PORT>') DO TaskKill.exe /F /PID %P
```
MACOSX and LINUX:
```bash
kill -kill `lsof -t -i tcp:<port`
```


### API
With 1.6, API has been introduced with many ways to customize and filter requests to your website and interaction with your server.     

##### Pocketmine, players, plugins, and levels variables with infos
First feature of the API is to get default infos about pocketmine or any plugin, players, or levels.    
This allow recognition from the webserver of the server.    
 - Pocketmine infos are located at $_POCKETMINE["info"]
 - Players infos are located at $_PLAYERS["name of the player"]["info"]
 - Levels infos are located at $_LEVELS["name of the level"]["info"];
 - Plugins infos are located at $_PLUGINS["name of the plugin"]["info"]

That being said, take a look at saved variables:
####### Pocketmine:
- software_name: Name of the software used as pocketmine (such as PocketMine-MP, Genisys, ...)
- software_codename: Codename of the software used as pocketmine (such as Unleached for PocketMine-MP ...)
- software_version: Version of the software used as pocketmine (such as 1.5, 1.6, ...)
- mcpe_version: MCPE version of the software.
- API: Plugins API version.
- software datapath: Root path of the server
- software_pluginpath: Plugins path of the server
- max_players: Maximum number of players in the server.
- port: Port listened by pocketmine.
- view_distance: View distance of the players on the server.
- ip: IP of the server. Might be usable.
- server_unique&#95;id: Returns the unique id of the server.
- auto_save: Boolean if the server has enabled autosaved
- default_gamemode: Default gamemode of the server.
- force_gamemode: Boolean if the server has enabled force gamemode.
- difficulty: Diffiulty of the server
- spawn_radius: Radius of the spawn protection.
- allow_flight: Boolean if the server enabled flight for players in every gamamode
- ultra_hardcore: Boolean if the server is in ultra hardcore, (banned if dead, no regeneration with food)
- motd: MOTD of the server.
- tps: Ticks Per Second of the server.
- lang_name: The name of the lang of the server.
- lang_code: The 3 char code letters of a language.

####### Plugins:

- name: Name of the plugin
- is_enabled: Is the plugin enabled
- data_folder: Path to plugin data folder
- apis: Compatible APIs of the plugin
- authors: Authors of the plugin
- prefix: Logger prefix of the plugin (mostly "[name of the plugin]")
- commands: Commands registered by the plugin
- dependencies: Plugin dependencies
- description: Description of the plugin
- load_before: When should the plugin loa (STARTUP or POSTLOAD)
- main: Main class of the plugin
- order: Order of the plugin
- soft_depend: Pocketmine dependency of the plugin
- version: Version of the plugin
- website: Website of the plugin.

####### Players:
- client_secret: Secret id of the client.
- banned: Is the player banned (should not be true, but we never know).
- whitelisted: Is the player whitelisted
- first_played: Date of the first play of the the player
- last_played: Date of the last play of the the player
- played_before: Has player played before
- allow_flight: Is the player allowed to fly
- flying: Is the player flying
- auto_jump: Has the player enabled auto jump
- op: Is the player OP
- connected: Is the player connected (Well he should x)).
- display_name: The displayed name of the player
- ip: Player's ip
- port: Port of the listening player's IP.
- is_sleeping: Is the player sleeping
- ticks_in&#95;air: Ticks in air of the player 
- gamemode: Gamemode of the player
- name: Name of the player
- loader_id: Id of the loader of the player.

####### Levels:
- tick_rate: Tick rate of the level
- id: Id of the level
- auto_save: Has the level auto save
- players: Players in the level with their info.
- time: Time in the level
- name: Name of the levels
- folder_name: Folder name of the player
- seed: Seed of the player

##### Passing and getting "arguments" to the thread.
Second API features are variables that you can pass to the webserver. Variables will be passed as $_PLUGINS["name"]["id of the variable"].      
To pass an argument, add this line to your code.
```php
$this->getServer()->getPluginManager()->getPlugin("Online")->setArgument($this, "id of the var", $varToPass);
```
You can see them by adding this line.
```php
$args = $this->getServer()->getPluginManager()->getPlugin("Online")->getArguments($this);
```
This gives you the ability to show custom things related to the server on the files.    
    

Please note that all variables passed (default & customs) are refreshed every 10 seconfs.
##### Custom handlers.
This is the most powerfull way to customize your webserver.     
Custom handlers are PHP files that are executed each time a request to the server is done.    
To add a custom handler, create a new PHP file into your plugin src and add this line of code to your main file:
```php
$this->getServer()->getPluginManager()->getPlugin("Online")->addHandler(&#95;&#95;DIR&#95;&#95; . "/handler.php");
```
You can also see all the handlers by doing:
```php
$handlers = $this->getServer()->getPluginManager()->getPlugin("Online")->getHandlers();
```
=======
That's it so I hoope you will enjoy my plugin !

(I know that @Falk created a plugin called Volt that does same but this plugin works in a completly different way (that make a lot less laggy and a lot less heavier in space !) and it's fully coded by myself in PHP7)
