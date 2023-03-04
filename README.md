## Robot script to display subscription profile (x-ui panel) v2.2 version

<br>

### [ترجمه به فارسی](README-persian.md)


### Possibilities

- Create accounts for users
- Fixed bugs in the previous version
- Display the notification when the subscription ends (to the user and admin)
- Get configuration information (for single port and multiple ports)
- Support for 4 x-ui panels
- Convert link to QrCode
- Display account name
- Status display
- Display the total volume
- Show download consumption
- Show upload consumption
- Show total volume usage
- Display the remaining volume
- Display the number of remaining days
- Show subscription expiration date
- Show input key
- Professional management panel
- Server management (create-delete-display)
-  ....

<br>

## Only supports the following panels:

- (Vexilo) both single-port
````
https://github.com/vaxilu/x-ui
````

- (asaadi) both single-port and multi-port

````
https://github.com/HexaSoftwareTech/x-ui
````

- (Nidoka Kalanka) both single yurt and multi-port

````
https://github.com/NidukaAkalanka/x-ui-english
````


- (Kafka) only single port

````
https://github.com/FranzKafkaYu/x-ui
````


<br>


### Prerequisite

- External hosting (cpanel)
- domain + ssl



<br>

## Important note about the address of the xui login panel:
Due to the limitation of some shared hosts, it is not possible to send requests from the script side to ports other than 80, 443, 8080 and some other ports for cPanel that are for http and https.
- Try to set port 8080 to be sure that it is active on the host

<br>

### learn inistallation

1. Open the following link in the browser and download the project
````
https://github.com/wizwizdev/wizwizxui-timebot/archive/refs/heads/main.zip
````

<br>

2. Create a bot inside the Bothfather bot and save the token somewhere
3. Enter the cpanel management panel
4. Click on MySQL® Databases and create a database
5. Then choose a username and password for the database and save it
6. Then go back to the main cpanel management page
7. Click on File Manager
8. Upload the project you downloaded and then exit the extract mode
9. Enter the address where the createDB.php file is located along with the domain in the browser and it should be a white page, in this case your database will be created:

````
https://yordomain.com/wizwizxui-timebot-main/createDB.php
````
Or if it is a subdomain:
````
https://sub.yordomain.com/wizwizxui-timebot-main/createDB.php
````


<br>


10. Edit the config.php file, get the database information, the bot token from Botfather and your numerical ID and Telegram channel (to send the notification of the end of user volume) through the bot get_id_bot and replace:
````
$Config = [
     'api_token' => "",
     'admin' => [],
     'report_channel' => -1000000 // -100xxxxxx
];
$Database = [
     'dbname' => "",
     'username' => "",
     'password' => ''
];
````

<br>


11. Now you need to set up the website, edit the following address and replace the token and address information, then run it in the browser:
````
https://api.telegram.org/bot1/setWebhook?url=2/bot.php
````
Instead of 1, you must enter the robot token and instead of 2, the address: Example
````
https://api.telegram.org/botHsMMWOqfNvYwuCU_1IzzCsQ34334/setWebhook?url=https://yordomain.com/wizwizxui-timebot-main/bot.php
````

<br>

12. Go back to the cpanel home page and click on the Cron Jobs button:
- Select Once Per Minute (* * * * *) mode in the Common Settings section
- In the Command field, please enter the following address:
````
/usr/bin/php -q address1 >/dev/null 2>&1
````
- Instead of addres1, you should put the address of the serverWarn.php file and save it. Example:
````
/usr/bin/php -q /home/yourfolder/public_html/yordomain.com/wizwizxui-timebot-main/serverWarn.php >/dev/null 2>&1
````

<br>


13. Repeat the same steps for the warnUsage.php file:
````
/usr/bin/php -q address2 >/dev/null 2>&1
````
- Instead of addres2, you should put the address of the serverWarn.php file and save it. Example:
````
/usr/bin/php -q /home/yourfolder/public_html/yordomain.com/wizwizxui-timebot-main/warnUsage.php >/dev/null 2>&1
````

<br>



14. Then start the robot, enter the uuid key and enjoy


### When adding a server to the robot, please enter the following address
````
- https://fanland.vpnroz-trojan.tk:8080
````

````
- https://fanland.vpnroz-trojan.tk:8080/path
````

````
- http://192.180.125:8080
````

#### The following address is wrong
````
- https://fanland.vpnroz-trojan.tk:8080/xui/inbounds
````

````
- https://fanland.vpnroz-trojan.tk:8080/
````


<br>

### Important:

- Do not share database, cPanel, and robot token information with anyone

<br>


Be sure to join the group and support us

## Contact Developer
💎 Group: https://t.me/wizwizdev
