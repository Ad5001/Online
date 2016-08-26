#Online
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

1. <u>Changing port</u>: Yes, it's not always easy to use port 80 ! It's often binded and stuffs... CHange the value of "port" in the config and set it to your new port and it will work fine !
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
=======
That's it so I hoope you will enjoy my plugin !

(I know that @Falk created a plugin called Volt that does same but this plugin works in a completly different way (that make a lot less laggy and a lot less heavier in space !) and it's fully coded by myself in PHP7)
