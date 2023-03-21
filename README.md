
# WizWiz

![](https://img.shields.io/github/v/release/wizwizdev/wizwizxui-timebot.svg)
![](https://visitor-badge.glitch.me/badge?page_id=wizwizdev.wizwizdev)


### [ترجمه به فارسی](README-persian.md)

<br>
<br>

### support me
<br>

Bank Sepe: Turan

```
5892101222351344
```

Tron: (TRX)

```
TY8j7of18gbMtneB8bbL7SZk5gcntQEemG
```

Bitcoin:

```
bc1qcnkjnqvs7kyxvlfrns8t4ely7x85dhvz5gqge4
```

Dogecoin:

```
DMyGMghEh4W55P3VeVHntCN3vYAFtshvVH
```

<br>


## Installation

- cpanel host or lionex server
- domain + ssl

<br>



<br>

## default settings

- Panel port and host or server must be 80-8080-54321
- If you encounter the following message or if the server inside the robot is not registered, please give a ticket to the hosting to open the desired ports.

````
Failed to connect to yourdomain.com port 80 after 340 ms: Couldn't connect to server
````

This error means that port 8080 of your panel is not open on the host or server and you need to open it


<br>

## Installation

````
https://github.com/wizwizdev/wizwizxui-timebot/archive/refs/heads/main.zip
````


> **Important note: After extracting the whole project, upload it directly from the wizwizxui-timebot-main folder to public_html**


<br>

## Database wizwiz.sql

- First, after creating the database, import the wizwiz.sql file into the created database

<br>


### Setting the baseInfo.php file



  ````
error_reporting(0);
$botToken = ''; //Replace the bot token
$dbUserName = ''; //Replace the database username
$dbPassword = ''; //Replace the database password
$dbName = ''; //Enter the database name
$admin = ; // Get the numeric ID or user ID of the admin account from this bot and replace it with get_id_bot
$channelLock = ""; //Replace the channel id with @ to force lock
$botUrl = "https://yourdomain.com/"; //Replace your domain
$walletwizwiz = ""; //Replace your card or wallet number
````

- To create a bot and receive a token via @bothfather bot, create a bot and replace the token
- To get an ID, get a number from the @chatIDrobot robot and then replace it
- For the channel, please replace the channel ID with @ (must be the channel manager robot to lock the channel)
- Also replace your domain with yourdomain.com
- And you can also put your Volt card or wallet number in "".



<br>

### cron job setting:


- Select Once Per Minute (* * * * *) mode in the Common Settings section
- In the Command field, please enter the following address:
````
/usr/bin/php -q address1 >/dev/null 2>&1
````
- Instead of addres1, you should put the address of the messagewizwiz.php file and save it. Example:
````
/usr/bin/php -q /home/yourfolder/public_html/yordomain.com/messagewizwiz.php >/dev/null 2>&1
````
or
````
/usr/bin/php -q /home/yourfolder/public_html/messagewizwiz.php >/dev/null 2>&1
````

<br>


- Repeat the same steps for the warnUsage.php file:

````
/usr/bin/php -q address2 >/dev/null 2>&1
````
- Instead of addres2, you should put the address of the warnUsage.php file and save it. Example:
````
/usr/bin/php -q /home/yourfolder/public_html/yordomain.com/warnUsage.php >/dev/null 2>&1
````
or
````
/usr/bin/php -q /home/yourfolder/public_html/warnUsage.php >/dev/null 2>&1
````

<br>


### set and hook


````
https://api.telegram.org/bot1/setWebhook?url=2/bot.php
````
Instead of 1, you should replace the robot token and instead of 2, enter the project address: eg
````
https://api.telegram.org/bot365447414:AAFjkjKJHoLKJIOJKLK89jklYwuCU_1IzzCsKJHKQvv/setWebhook?url=https://yordomain.com/wizwizxui-timebot-main/bot.php
````

- If the following text is displayed in the output, then congratulations, you have executed the robot correctly

````
{"ok":true,"result":true,"description":"Webhook was set"}
````


<br>

## Setting the htaccess file to increase security

- After extracting the project files, you may not be able to see the htaccess file, first click on Settings in the upper right corner
- In the window that opens, activate the option Show Hidden Files (dotfiles) and then click save
- Finally, remove the .htaccess file from the bot folder and place it directly in public_html


<br>

## Set config port count

- Edit the temp.txt file and write your desired value

<br>



## Important points after installation:

- To forcefully lock the bot, you must be the channel administrator
- Lookish host or server must be outside of Iran
- If you use the Trojan protocol (the panel must have the ability to create Trojans, otherwise you will face problems)
- The validity of the notification becomes zero after 2 days (after two days, if the volume or time is low, it will be sent again)
- If the public message or notification is not sent when setting the cron job (when setting the cron job, just delete the domain address from inside the command)
- To create a test configuration, set the price to 0 (each account can use the free test account only once
- If you encounter the message (Glam, the connection to the server is not established), remember that the path is next to the address of the panel, remove the path and restart the panel, the problem will be solved.





<br>

## Support for the following panels:


#### Important note: Install Sanai panel version 1.0.9 and Alireza panel version 0.4.0 that we have installed



- (Nidoka Kalanka) single-port, multi-port (the best option for the robot)
````
bash <(curl -Ls https://raw.githubusercontent.com/NidukaAkalanka/x-ui-english/master/install.sh)
````
- (Senai) multi-port - single-port
````
bash <(curl -Ls https://raw.githubusercontent.com/mhsanaei/3x-ui/master/install.sh) v1.0.9
````
- (Alireza) multi-port - single port
````
bash <(curl -Ls https://raw.githubusercontent.com/alireza0/x-ui/master/install.sh) 0.4.0
````
- (Vexilo) only single port
````
bash <(curl -Ls https://raw.githubusercontent.com/vaxilu/x-ui/master/install.sh)
````

<br>


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

### Setting the certificate inside the robot

- Instead of keyFile and certificateFile, please enter the address of your certificate and the following is an example:

````
{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}]}
````

Example:

- Important note: do not leave a space between the ssl address and it should be without space, otherwise the entire x-ui panel will fail.

````
{"serverName": "yourdomain.com","certificates": [{"certificateFile": "/root/cert.crt","keyFile": "/root/private.key"}]}
````

- serverName: yourdomain.com
- certificateFile: /root/cert.crt
- keyFile: /root/private.key


<br>



### Vizviz facilities


- Automatic sale of vless - vmess - trojan
- Setting and creating a configuration with the ability to:
- (volume - day - network - protocol - single user {depends on the panel})
- Server creation and management:
- (name-flag-remark-capacity-header-request-request-tls-sni-ip)
- Create category and manage it
- Creating a plan and managing it
- Create configuration of shared port and dedicated port
- Create test configuration for users (before purchase)
- Ability to pay by card (confirmed by the manager)
- Automatic sending of configuration along with link + configuration name + qrcode to the user
- View the complete specifications of the purchased configuration
- Show the sold accounts of each plan
- Advanced ticketing system section (ticketing)
- Display capability (software link)
- Sending public messages with CronJob
- Enable or disable (Sales - Configuration specifications or both)
- Notification of completion of volume and configuration time (only to the user)
- Inline (config specifications)
- Forced channel lock
- Senate panel support
- Alireza panel support
- Vexilo panel support
- Niduka panel support
- The possibility of adding an account by the user
- The ability to manage and delete the account by the user
- The possibility of registering the configuration to vless-vmess-uuid (Trojan does not support it well)
- Get configuration information (for single port and multiple ports)
- Display account name
- Show input key
- Status display
- Display the total volume
- Show download consumption
- Show upload consumption
- Show total volume usage
- Display the remaining volume
- Display the number of remaining days
- Show subscription expiration date

<br>

Be sure to join the group and support us

## Contact Developer
💎 Group: https://t.me/wizwizdev
