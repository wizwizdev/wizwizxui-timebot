## Bot script to show subscription profile (x-ui panel) version 3.0


![](https://visitor-badge.glitch.me/badge?page_id=wizwizdev.wizwizdev)


### [ترجمه به فارسی](README-persian.md)

<br>


## Support for the following panels:


- (Sanai) single yurt, multi-port

````
https://github.com/MHSanaei/3x-ui
````
- (Alireza) single yurt, multi-port
````
https://github.com/alireza0/x-ui
````

- (Vaksilo) only single yurt
````
https://github.com/vaxilu/x-ui
````
- (SD) single yurt, multi-port

````
https://github.com/HexaSoftwareTech/x-ui
````
- (Nidoka Kalanka) single yurt, multi-port

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

- Due to the limitation in some shared hosts, it is not possible to send requests from the script side to ports other than 80, 443, 8080 and some other ports for cPanel that are for http and https.
- Try to set port 8080 to be sure that it is active on the host, if it does not work, send a message to the hosting support to activate it for you.



<br>


## learn inistallation


#### 1. Open the following link in the browser and download the project


````
https://github.com/wizwizdev/wizwizxui-timebot/archive/refs/heads/main.zip
````


<br>


#### 2. Log in to the cpanel management panel and create a database according to [Database creation tutorial] (README-DB.md)

#### 3. After creating the database, go back to the main page of cpanel administration

#### 4. Click on File Manager

#### 5. Upload the project you downloaded and then exit the extract mode


#### 6. Edit the config.php file

<br>

Get the database information, bot token from Botfather and admin numeric ID and telegram channel (to send user volume notification) from get_id_bot bot.


Get the original ID of the channel without @ (to forcefully lock the channel) and replace it with:

<br>

````
$Config = [
     'api_token' => "",
     'admin' => [],
     'report_channel' => - , // -100xxxxxx
     'channel_lock' => ['']
];
$Database = [
     'dbname' => "",
     'username' => "",
     'password' => ''
];
````

for example:

````
$Config = [
     'api_token' => "365447414:AAFjkjKJHoLKJIOJKLK89jklYwuCU_1IzzCsKJHKQvv",
     'admin' => [2525252525],
     'report_channel' => -3656542000 , // -100xxxxxx
     'channel_lock' => ['wizwizdev']
];
$Database = [
     'dbname' => "wizwiz_wizbot",
     'username' => "wizwiz_hajmbot",
     'password' => '123456789'
];
````


<br>


#### 7. Enter the address where the createDB.php file is located along with the domain in the browser and it should be a white page, in this case your database will be created:


````
https://yordomain.com/wizwizxui-timebot-main/createDB.php
````

Or if it is a subdomain:
````
https://sub.yordomain.com/wizwizxui-timebot-main/createDB.php
````


<br>


#### 8. Now you need to set the webhook, edit the following address and replace the token and address information, then run it in the browser:


````
https://api.telegram.org/bot1/setWebhook?url=2/bot.php
````
Instead of 1, you must enter the robot token and instead of 2, the address: Example
````
https://api.telegram.org/bot365447414:AAFjkjKJHoLKJIOJKLK89jklYwuCU_1IzzCsKJHKQvv/setWebhook?url=https://yordomain.com/wizwizxui-timebot-main/bot.php
````

<br>

#### 9. Go back to the cpanel home page and click on the Cron Jobs button:


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


#### 10. Repeat the same steps for the warnUsage.php file:

````
/usr/bin/php -q address2 >/dev/null 2>&1
````
- Instead of addres2, you should put the address of the serverWarn.php file and save it. Example:
````
/usr/bin/php -q /home/yourfolder/public_html/yordomain.com/wizwizxui-timebot-main/warnUsage.php >/dev/null 2>&1
````

<br>



#### 11. Then start the robot, give the uuid key or VMESS-VLESS link and enjoy


##### For the notification section of the end of the user volume, the user must log in to his account in the robot to receive notifications, and the administrator must make the robot the channel manager so that the notifications will be notified in the channel.

### When adding a server to the robot, please enter the following address
````
https://youdomain.com:8080
````

````
https://youdomain.com:8080/path
````

````
http://192.180.125:8080
````


#### The following address is wrong

````
https://youdomain.com:8080/xui/inbounds
````

````
https://youdomain.com:8080/
````



<br>

### Important:

- Do not share database, cPanel, and robot token information with anyone

<br>


### Features of version 3.0

- Appearance inlining (config specifications)
- View user statistics (general, log in to the panel, only robot members)
- Mandatory locking of one or more channels
- Added MHSanaei panel
- The addition of Alireza's panel (alireza0)
- The possibility of adding an account by the user
- The ability to manage and delete the account by the user
- Possibility to register config to vless, vmess and trojan (except uuid)
- Added the ability to send messages to support
- Added the ability to turn off and turn on the robot (when necessary)

### Features of version 2.0

- Account feature for users
- Fixed bugs in the previous version
- Display the notification when the subscription ends (to the user and admin)
- Get configuration information (for several ports)
- Convert link to QrCode
- Display account name
- Add channel for notification message
- Show input key
- Professional management panel



### Features version 1.0


- Get configuration information (for single port)
- Support for 4 x-ui panels (Vexilo-SD-Nidoka-Kafka {single port})
- Status display
- Display the total volume
- Show download consumption
- Show upload consumption
- Show total volume usage
- Display the remaining volume
- Display the number of remaining days
- Show subscription expiration date
- Server management (create-delete-display)

<br>

Be sure to join the group and support us

## Contact Developer
💎 Group: https://t.me/wizwizdev
