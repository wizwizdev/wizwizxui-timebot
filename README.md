<p align="center">
  <a href="https://github.com/wizwizdev/wizwizxui-timebot" target="_blank" rel="noopener noreferrer">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://user-images.githubusercontent.com/27927279/227711552-d2bc1089-5666-477b-9be7-d7e50a5286dc.png">
      <img width="200" height="200" src="https://user-images.githubusercontent.com/27927279/227711552-d2bc1089-5666-477b-9be7-d7e50a5286dc.png">
    </picture>
  </a>
</p> 

<p align="center">
	<a href="./README.md">
	English
	</a>
	/
	<a href="./README-fa.md">
	فارسی
	</a>

</p>

<h1 align="center"/>Welcome to wizwiz</h1>

<p align="center">
Easy to sell with <a href="https://github.com/wizwizdev/wizwizxui-timebot">wizwizxui-timebot</a> easy install with few clicks
</p>

<p align="center">
wizwiz is a powerful and professional robot that supports several types of panels and is the best option for sale, supports most protocols and has easy installation. This robot is optimized for the dear people of Iran. It is a great alternative to selling so that you can get to work.
</p>


<div align=center>

[![Telegram Channel](https://img.shields.io/endpoint?label=Channel&style=flat-square&url=https%3A%2F%2Ftg.sumanjay.workers.dev%2Fwizwizch&color=blue)](https://telegram.dog/wizwizch)
[![Telegram Group](https://img.shields.io/endpoint?color=neon&label=Support%20Group&style=flat-square&url=https%3A%2F%2Ftg.sumanjay.workers.dev%2Fwizwizdev)](https://telegram.dog/wizwizdev)
<img src="https://img.shields.io/github/license/wizwizdev/wizwizxui-timebot?style=flat-square" />
<img src="https://img.shields.io/github/v/release/wizwizdev/wizwizxui-timebot.svg" />
<!-- <img src="https://visitor-badge.glitch.me/badge?page_id=wizwizdev.wizwizdev" />
 -->
</div>

<br>
<br>
    <a align="center">
        <img src="https://github.com/wizwizdev/wizwiz-xui-pro/assets/27927279/c8e45ef9-2b4d-4a3e-aa5a-40df4f61abf3" />
    </a>     
<br>
<br>

# Installation Ubuntu-20.4 


- If your server does not have root access, please grant root access with "sudo -i" command and then install
- Create a bot in @botfather and /start it
- The first option asks you for a domain, you must set the ip server for the domain and then enter it according to the example
> Enter the installation command in the console and enter the required items to complete the installation.
```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/wizwiz.sh)
```
- First enter "sub.domain.com" or "domain.com" without https
- Enter email
- Enter y
- Enter 2
- Enter username database
- Enter password database
- Enter token
- Enter Numerical ID of admin from @userinfobot
- Re-enter "sub.domain.com" or "domain.com" without https
- Very good, the installation message ( ✅ The wizwiz bot has been successfully installed! ) is sent to the bot

<br>
<br>

## Update bot - Update panel - backup - remove wizwiz

- With every update and backup, a notification is sent to the manager robot
```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/update.sh)
```

<br>

<hr>

<br>

<h2 align="center">
<a href="https://t.me/wizwizch/193">Installation tutorial on Ubuntu server</a>
</h2>

<br>

<h2 align="center">
<a href="https://t.me/wizwizch/192">Installation tutorial on cPanel host</a>
</h2>



<br>
<hr>
<br>



## The instructions for installing on the host, download the project through the link below

````
https://github.com/wizwizdev/wizwizxui-timebot/archive/refs/heads/main.zip
````

<br>


# Important items in the host

## 1- Error 500 in cPanel host

> In the cpanel host, click on the Select PHPVersion option and activate the following options in the extension section:
- pdo_mysql
- mysqlnd
- nd_mysqli
> Disable the following options:
- mysqli
- nd_pdo_mysql
### If the following option is blue, please activate it:

<br>
     <a align="center">
         <img src="https://user-images.githubusercontent.com/27927279/230842783-16f6d1a5-e726-4533-a57b-98cb04fa8dfc.PNG" />
     </a>
<br>
<br>

## 2- Activation of the following extensions

- soap (for the payment gateway)
- ssh2 (to back up from the panel) for some hosts, this does not exist and you cannot use the backup function
- fileinfo ( Token not found error )


<br>


## 3- Notes after installation

- After installation, be sure to completely delete the install folder and the create DB.php file inside the wizwizxui-timebot-main folder


<br>

## 4- Kronjob for the following files

- messagewizwiz.php
- rewardReport.php
- warnusers.php
- backupnutif.php

````
/usr/bin/php -q /home/wizwizro/public_html/wizwizxui-timebot-main/settings/messagewizwiz.php >/dev/null 2>&1
````


- instead of wizwizro, you should take the desired address from the host according to the image below and enter it


<p align="center">
     <img src="https://user-images.githubusercontent.com/27927279/229339959-3da695e6-eee8-49b0-a520-37552d50090f.PNG" />
</p>



- You must create a separate cron job for each of the files warnusers.php - rewardReport.php - messagewizwiz.php, but it is different for the backupnutif.php file that you must do as follows


````
/usr/bin/php -q /home/wizwizro/public_html/panel_folder_name/backupnutif.php >/dev/null 2>&1
````


<br>
<hr>
<br>

# Important Points

- To force lock the channel, make sure the bot is the admin of the channel and give it full admin access (tick them all)
- If you install the Vizuise panel on the host, be sure to activate the ssh2 extension to perform the backup work.
- To use the NowPayment portal, the charge amount must be above 3.5 dollars because it cannot be paid below 3.5 dollars.
- Lokishhost or Linux should not be hosted in Iran (because Telegram is restricted and censored in Iran)
- If you use trojan protocol, your x-ui panel must support trojan, otherwise your panel will have problems.
- If the remaining traffic of the service reaches one gigabyte and the remaining time reaches one day, a notification is sent to the user.
- If the user does not renew the service within 48 hours, the service deletion notification will be sent to the user and the service will be deleted.
- To create a test account, set the price to 0, each user can have a test account only once
- To use HTTP and Header in the robot, you must set the value of Header Type to http and enter the value of Host:domain.ir for the request header.
- If we use reality, after registering the plan, please edit the plan and enter the desired dest and servername value.
- If you use the tunnel, be sure to read the <a href="https://t.me/wizwizch/177">text</a> inside the channel carefully.
- If you encounter an error (your connection to the server is not established) while shopping, be sure to listen to <a href="https://t.me/wizwizch/186">Voice</a> inside the channel.



<br>
<hr>
<br>



# Supported Panels


- (Niduka Akalanka)
````
bash <(curl -Ls https://raw.githubusercontent.com/NidukaAkalanka/x-ui-english/master/install.sh)
````
- (Sanaei)
````
bash <(curl -Ls https://raw.githubusercontent.com/mhsanaei/3x-ui/master/install.sh)
````
- (Alireza)
````
bash <(curl -Ls https://raw.githubusercontent.com/alireza0/x-ui/master/install.sh)
````
- (Vaxilu)
````
bash <(curl -Ls https://raw.githubusercontent.com/vaxilu/x-ui/master/install.sh)
````

- The rest of the panels probably do not support it because it has not been tested, so you can test and use it yourself




<br>
<hr>
<br>



# Donation

- Sepe Bank: `5892101222351344`
- Tron (TRX): `TY8j7of18gbMtneB8bbL7SZk5gcntQEemG`
- Bitcoin: `bc1qcnkjnqvs7kyxvlfrns8t4ely7x85dhvz5gqge4`
- Dogecoin: `DMyGMghEh4W55P3VeVHntCN3vYAFtshvVH`

<br>
<hr>
<br>

# Features

- nowpayments - zarinpal - nextpay - weswap currency exchange portal
- Support for - xtls - tls - reality - Grpc - ws - tcp
- Support vless - vmess - trojan
- The possibility of extending the service
- Smart subscription
- Filtering status of servers
- Automatic location change
- Increasing volume and service time
- Ability to pass
- Ability to order the desired plan by the user
- Authentication of Iranian and foreign contact numbers
- Backup x-ui panel
- Subcategory and commission
- Create discount and gift codes
- Ability to track the user
- Create button and answer for it
- Config output with different ip or domain
- Ability to change protocol and network type
- Setting the config port randomly or automatically
- Wallet (possibility of charging - balance transfer)
- Send notification of new member in robot to (admin)
- Display user information (user-admin)
- The ability to send private messages from the admin to the user
- Ability to manage and view servers - categories - plans
- Ability to block and release
- Ability to add admin
- Display the inventory of servers
- The ability to send income reports to the channel
- Sending public messages
- Receive sold configurations
- Create shared port and dedicated port configuration
- Test account for users
- Card to card functionality
- Display the sold accounts of each plan
- Display capability (software link)
- Send public messages with CronJob
- Notifying the end of volume and configuration time (to the user)
- Forced channel lock
- Ability to get link details
- Off/on capability (all robot features)
- Notification of purchase information + renewal, etc. in full to the admin robot


<br>
<hr>
<br>

Be sure to join the group and channel and support us

## Contact Developer
💎 Group: https://t.me/wizwizdev
💎 Channel: https://t.me/wizwizch

<br>
<br>

## Stargazers over time

[![Stargazers over time](https://starchart.cc/wizwizdev/wizwizxui-timebot.svg)](https://starchart.cc/wizwizdev/wizwizxui-timebot)
