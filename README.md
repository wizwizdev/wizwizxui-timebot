<p align="center">
  <a href="https://github.com/wizwizdev/wizwizxui-timebot" target="_blank" rel="noopener noreferrer">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://user-images.githubusercontent.com/27927279/227711552-d2bc1089-5666-477b-9be7-d7e50a5286dc.png">
      <img width="200" height="200" src="https://user-images.githubusercontent.com/27927279/227711552-d2bc1089-5666-477b-9be7-d7e50a5286dc.png">
    </picture>
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
<img src="https://visitor-badge.glitch.me/badge?page_id=wizwizdev.wizwizdev" />

</div>

<br>
    <a align="center">
        <img src="https://user-images.githubusercontent.com/27927279/230026376-100851a4-07b4-4695-aac2-3734643dac3f.PNG" />
    </a>     
<br>
  
<br>


## Installation Ubuntu-20.4 

<details markdown="1"> <summary><h3>Installation English</h3></summary>
 

- If your server does not have root access, please grant root access with "sudo -i" command and then install

```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/wizwiz.sh)
```
> Enter the installation command in the console and enter the required items to complete the installation.
- The first option asks you for a domain, you must set the ip server for the domain and then enter it according to the example:
- `sub.domain.com` or `domain.com`
- Enter email
- Enter y
- Enter 2


```
mysql -u root -pPASSWORD -e "alter user 'root'@'localhost' identified with mysql_native_password by 'PASSWORD';FLUSH PRIVILEGES;"
```
> Replace the above command with the appropriate password instead of PASSWORD and enter the console after the installation command.

```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/database.sh)
```
> Finally, enter the final commandment in the console and create a username and password for your database.

<br>

## Update - backup

```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/update.sh)
```
> Whenever there is an update, the files are automatically updated by executing the following command


</details>

<details markdown="2"> <summary><h3>Installation Persian</h3></summary>
 
- اگر سرور شما دسترسی روت ندارد، لطفا با دستور "sudo -i" دسترسی روت بدهید و سپس نصب کنید

```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/wizwiz.sh)
```
> دستور نصب را در کنسول وارد کرده و موارد مورد نیاز را برای تکمیل نصب وارد کنید.
- گزینه اول از شما یک دامنه می خواهد، باید ip server را برای دامنه تنظیم کنید و سپس مطابق مثال وارد کنید:
- `sub.domain.com` یا `domain.com`
- ایمیل را وارد کنید
- y را وارد کنید
- 2 را وارد کنید

```
mysql -u root -pPASSWORD -e "alter user 'root'@'localhost' identified with mysql_native_password by 'PASSWORD';FLUSH PRIVILEGES;"
```
> دستور بالا را به جای PASSWORD پسورد مناسب را جایگزین کنید و پس از دستور نصب وارد کنید.

```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/database.sh)
```
> دستور نهایی برای ایجاد دیتابیس را در کنسول وارد کنید و یک نام کاربری و رمز عبور برای پایگاه داده خود ایجاد کنید.

<br>

## Update - backup

```
bash <(curl -s https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/update.sh)
```
> هر زمان که آپدیت وجود داشته باشد با اجرای دستور زیر فایل ها به صورت خودکار آپدیت می شوند

</details>


<br>


## Installation on cPanel host

<details markdown="4"> <summary><h3>Installation English</h3></summary>

# Installation on cPanel host

<br>


## Contents

- [Prerequisite (1)](#prerequisite-1)
- [Download (2)](#download-2)
- [Project upload (3)](#upload-project-3)
- [Create Database (4)](#create-database-4)
- [Payment portal (5)](#payment-portal-5)
- [Install (6)](#install-6)
- [Cron Job (7)](#cron-job-7)


<br>


## Prerequisite 1

- cpanel host
- Domain with ssl enabled

<br>


## Download 2


- Download the project through the link below

````
https://github.com/wizwizdev/wizwizxui-timebot/archive/refs/heads/main.zip
````

<br>


## Upload Project 3

- Enter the cpanel host
- Upload the project directly into the public_html folder on the domain or subdomain you want to install.
- Click on the uploaded zip file and press Extract to decompress it
- Before installing, make sure the php version is 7.4. To set it in the cpanel host, click on the PHP Selector option and set the domain related to the robot.
- In the cpanel host, click on the Select PHPVersion option and make sure to activate Soap in the extension section


<br>


## create Database 4


- To create a database, please click on [Training database](education/DB.md)



<br>


## Payment Portal 5

- Register for the NowPayment currency portal on the nowpayments.io site with an email and enter the API Keys in the bot registration form (no authentication)
- Register for the Zarin Pal portal on the zarinpal.com website and enter the merchant code in the robot registration form (it has authentication)


<br>


## Install 6

- Create a bot in botfather and be sure to start the bot once, then follow the steps below
- To install the robot, enter the following address in the browser
- Instead of yourdomain.com, please replace the domain or subdomain of the host where you uploaded the project and run it in the browser.

````
https://yourdomain.com/wizwizxui-timebot-main/install/install.php
````

<br>

- Click on the install button

<br>

<p align="center">
     <a>
         <img src="https://user-images.githubusercontent.com/27927279/228797072-00d075f6-24d8-428b-9c5d-479aec9eabc9.PNG" />
     </a>
</p>

<br>

- Then enter the required information and click on the install bot button to complete the installation process
- If you install the robot correctly, the message (the robot has been successfully installed) will be sent to the robot and that's it

<br>

## Cron Job 7

- Log in to the host, then click on the Cron Jobs option and set the cron job according to the settings below
- Select Once Per Minute (* * * * *) mode in the Common Settings section
- In the Command field, please enter the following address:


````
/usr/bin/php -q /home/wizwizro/public_html/wizwizxui-timebot-main/settings/messagewizwiz.php >/dev/null 2>&1
````


- instead of wizwizro, you should take the desired address from the host according to the image below and enter it


<p align="center">
     <img src="https://user-images.githubusercontent.com/27927279/229339959-3da695e6-eee8-49b0-a520-37552d50090f.PNG" />
</p>



- You must create a separate cron job for each of the files warnusers.php - rewardReport.php - messagewizwiz.php

</details>


<details markdown="3"> <summary><h3>Installation Persian</h3></summary>


# نصب بر روی هاست سی پنل

<br>





## فهرست

- [پیش نیاز ( مرحله اول ) ](#پیش-نیاز-مرحله-اول) 
- [دانلود ( مرحله دوم ) ](#دانلود-مرحله-دوم) 
- [آپلود پروژه ( مرحله سوم ) ](#آپلود-پروژه-مرحله-سوم) 
- [ایجاد دیتابیس ( مرحله چهارم ) ](#ایجاد-دیتابیس-مرحله-چهارم) 
- [درگاه پرداخت ( مرحله پنجم ) ](#درگاه-پرداخت-مرحله-پنجم)
- [نصب ( مرحله ششم ) ](#نصب-مرحله-ششم) 
- [کرون جاب ( مرحله هفتم ) ](#کرون-جاب-مرحله-هفتم)
- [فایل htaccess](#فایل-htaccess)


<br>


## پیش نیاز مرحله اول

- هاست cpanel
- دامنه با ssl فعال

<br>

## دانلود مرحله دوم

- پروژه رو از طریق لینک زیر دانلود کنید

````
https://github.com/wizwizdev/wizwizxui-timebot/archive/refs/heads/main.zip
````

<br>


## آپلود پروژه مرحله سوم

- وارد هاست cpanel بشید
- پروژه را مستقیم داخل پوشه public_html که حالا روی دامین یا ساب دامینی که می خواهید نصب کنید آپلود کنید
- روی فایل zip آپلود شده کلیک کنید و گزینه Extract را بزنید تا از حالت فشرده خارج شود
- قبل از نصب حتما ورژن php روی 7.4 باشد برای تنظیم داخل هاست cpanel روی گزینه PHP Selector کلیک کنید و دامنه مربوط به ربات را تنظیم کنید  
- داخل هاست cpanel روی گزینه Select PHPVersion کلیک کنید و در بخش extension حتما Soap را فعال کنید


<br>


## ایجاد دیتابیس مرحله چهارم


- برای ایجاد دیتابیس لطفا روی [آموزش دیتابیس](DB.md) کلیک کنید



<br>


## درگاه پرداخت مرحله پنجم

- برای درگاه ارز NowPayment در سایت nowpayments.io با ایمیل ثبت نام کنید و API Keys را در فرم ثبت نامی ربات وارد کنید ( احراز هویت ندارد ) 
- برای درگاه زرین پال در سایت zarinpal.com ثبت نام کنید و کد مرچنت را در فرم ثبت نامی ربات وارد کنید ( احراز هویت دارد )


<br>


## نصب مرحله ششم

- یک ربات را در botfather ایجاد کنید و حتما حتما یک بار ربات را /strat کنید سپس مراحل پایین را انجام بدید
- برای نصب ربات از آدرس زیر را در مرورگر وارد کنید
- به جای yourdomain.com لطفا دامین یا ساب دامین هاستی که پروژه را آپلود کرده اید جایگزین کنید و در مرورگر اجرا کنید

````
https://yourdomain.com/wizwizxui-timebot-main/install/install.php
````

<br>

- روی دکمه نصب کلیک کنید

<br>

<p align="center">
    <a>
        <img src="https://user-images.githubusercontent.com/27927279/228797072-00d075f6-24d8-428b-9c5d-479aec9eabc9.PNG" />
    </a>
</p>

<br>

- سپس اطلاعات خواسته شده را وارد کنید و روی دکمه نصب ربات کلیک کنید تا مراحل نصب انجام شود
- اگر ربات را به درستی نصب کنید پیغام ( ربات با موفقیت نصب شد ) برای ربات ارسال می شود و تمام

<br>

## کرون جاب مرحله هفتم

- وارد هاست بشید سپس روی گزینه Cron Jobs کلید کنید و طبق تنظیمات زیر کرون جاب را تنظیم کنید
- در قسمت Common Settings حالت Once Per Minute(* * * * *) را انتخاب کنید
- در قسمت Command لطفا ادرس زیر را وارد کنید:


````
/usr/bin/php -q /home/wizwizro/public_html/wizwizxui-timebot-main/settings/messagewizwiz.php >/dev/null 2>&1
````


- به جای wizwizro باید آدرس مورد نظرتون رو طبق تصویر زیر از هاست بردارید و وارد کنید 


<p align="center">
    <img src="https://user-images.githubusercontent.com/27927279/229339959-3da695e6-eee8-49b0-a520-37552d50090f.PNG" />
</p>



- برای هر کدام از فایل های warnusers.php - rewardReport.php - messagewizwiz.php باید کرون جاب جدا ایجاد کنید


</details>

<br>
<br>

## Robot settings

<details markdown="4"> <summary><h3>English</h3></summary>

<br>


## Contents

- [Htaccess File](#htaccess-file)
- [Bot Installation Errors](#bot-installation-errors)
- [Important Tips](#important-points)
- [Robot Settings](#robot-settings)
- [Shared Port](#shared-port)
- [Fixing the Panel Error](#fixing-the-panel-error)
- [Supported Panels](#supported-panels)


<br>


## Htaccess File

- After extracting the project files, you may not be able to see the htaccess file, first click on Settings in the upper right corner
- In the opened window, activate the option "Show Hidden Files (dotfiles)" and then click save, now you can see the file
- If no trace of the htaccess file is found, upload it again

<br>



## Bot Installation Errors


#### Error: The database cannot connect to the database

- The user name or database name or password information is wrong and must be corrected


#### Error: The bot must be installed on a domain with active ssl

- Your domain does not have ssl and you need to enable it



#### Error: No bot found with this token

- This token is wrong and you must enter the token correctly



#### Error: The required files of the robot could not be found

- The robot cannot find the createDB.php file. Make sure it is inside the project, then install it


### Error: 500 after installing the bot

<br>
     <a align="center">
         <img src="https://user-images.githubusercontent.com/27927279/230745829-73c323f7-46ff-4680-8f86-25ab1f026734.PNG" />
     </a>
<br>


##### In the cpanel host, click on the Select PHPVersion option and activate the following options in the extension section:
- pdo_mysql
- mysqlnd
- nd_mysqli
##### Disable the following options:
- mysqli
- nd_pdo_mysql

##### If the following option is blue, please activate it:

<br>
     <a align="center">
         <img src="https://user-images.githubusercontent.com/27927279/230842783-16f6d1a5-e726-4533-a57b-98cb04fa8dfc.PNG" />
     </a>
<br>


## Important Points


- To forcefully lock the channel, make sure the robot is a channel admin and give it all of the admin rights (tick all of them)
- Before installing, make sure the php version is 7.4. To set it in the cpanel host, click on the PHP Selector option and set the domain related to the robot.
- In the cpanel host, click on the Select PHPVersion option and make sure to activate Soap in the extension section
- To use the NowPayment portal, the charge amount must be above 3.5 dollars because it cannot be paid below 3.5 dollars.
- Lokishhost or Linux server must not be hosted in Iran (because Telegram is restricted and censored in Iran)
- If you use the Trojan protocol, your x-ui panel must support Trojan, otherwise your panel will run into problems.
- The validity of the config notification becomes zero after 2 days (that is, if the service is extended, the notification of expiration will be activated again)
- If the remaining traffic of the service reaches one gb and the remaining time reaches one day, a notification will be sent to the user
- If the user does not renew the service within 48 hours, a service deletion notification will be sent to the user and the service will be deleted
- If the public message or notification is not sent when setting the cron job (when setting the cron job, just delete the domain address from inside the command)
- To create a test configuration, set the price to 0, each user is restricted to one test config per each telegram account
- Panel type (Sanaei and Alireza0) is used for Sanaei version 1.1.1 and above and Alireza0 version 0.4.2
- The panel type (simple) is used for Sanaei version 1.0.9 and below, Alireza 0.3.2 and below, Vaxilu x-ui, Niduka
- To use HTTP and Header in the robot, you must set the value of Header Type to http and enter the value of Host:domain.ir for the request header.
- To close sales on a server, you can set the server balance to 0
- To set the income notification channel, click on the ID inside the glass keyboard and set the channel again
- To reinstall the robot, you must download the files from the beginning (the previous files do not work because you have a new file named baseinfo.php)



<br>

## Robot Settings


#### To register the server, observe the following points:

- Use a port that is open on the host, if you are not sure, send a ticket to the hosting support and ask for open ports on the server, usually port 8080 is open on most hosts.
- If your panel uses a domain, the ssl of the panel must be active and start it with https
- If your panel uses an IP address, please delete the two boxes in the settings of the panel according to the image below and save and restart the panel, and to register the server in the robot, you must enter it as http


<a align="center">
     <img src="https://user-images.githubusercontent.com/27927279/228873312-7ac5f12a-5d67-465f-a106-45a11f8f82ee.PNG" />
</a>


<br>

#### Setting Protocol and Network:


Pay attention, if you set the tls-xtls settings like this, you must create this protocol and network when registering the plan.



- vless `ws - tcp ( tls - xtls )`
- vmess `ws - tcp ( tls - xtls )`
- vless `Grpc ( tls )`
- vmess `Grpc ( tls )`
- trojan `tcp ( xtls )`





#### When adding a server to the bot, please enter the following address




````
https://youdomain.com:54321
````

````
http://192.180.125:54321
````

````
https://youdomain.com:54321/path
````

#### The following address is wrong

````
https://youdomain.com:54321/xui/inbounds
````

````
https://youdomain.com:54321/
````


<br>

#### Setting the certificate inside the robot


- tls: `{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}]}`


- xtls: `{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}],"alpn": []}`

Example:

- Important note: do not leave a space between the ssl address and it should be without space, otherwise the entire x-ui panel will fail.

- serverName: yourdomain.com
- certificateFile: /root/cert.crt
- keyFile: /root/private.key


````
{"serverName": "yourdomain.com","certificates": [{"certificateFile": "/root/cert.crt","keyFile": "/root/private.key"}]}
````

````
{"serverName": "yourdomain.com","certificates": [{"certificateFile": "/root/cert.crt","keyFile": "/root/private.key"}],"alpn": [ ]}
````

<br>


## Shared Port


- With one output, you can receive as many configs as you want on different domains (for different internet providers)
- In order to use a special or shared port, first manually create a configuration with a specific port and in the shared plan of the robot, give the id of the configuration line to the robot so that it will automatically create the configurations on the port.


<br>

## Fixing the Panel Error

- If you encounter such an error, please do the following carefully


<br>


<p align="center">
     <a>
         <img src="https://user-images.githubusercontent.com/27927279/228843013-e06c3655-1fc9-44aa-a256-30d0d4a9a784.jpg" />
     </a>
</p>


<br>

- First, download the Navicat software or DB Browser for SQLite (you have to crack the Navicat software, but it has a nice user interface)
- Download the x-ui panel database file, which is in x-ui.db format, from the server which is in the path etc/x-ui/x-ui.db
- Enter the x-ui.db database file into the desired software, then delete the last user configuration and save the project
- Upload the new file to the server and restart the panel once, the problem will be solved


<br>

## Supported Panels


- (Niduka Akalanka) single-port, multi-port (the best option for the robot)
````
bash <(curl -Ls https://raw.githubusercontent.com/NidukaAkalanka/x-ui-english/master/install.sh)
````
- (Sanaei) multi-port - single-port
````
bash <(curl -Ls https://raw.githubusercontent.com/mhsanaei/3x-ui/master/install.sh)
````
````
bash <(curl -Ls https://raw.githubusercontent.com/mhsanaei/3x-ui/master/install.sh) v1.0.9
````
- (Alireza0) multi-port - single port
````
bash <(curl -Ls https://raw.githubusercontent.com/alireza0/x-ui/master/install.sh)
````
````
bash <(curl -Ls https://raw.githubusercontent.com/alireza0/x-ui/master/install.sh) 0.4.0
````
- (Vaxilu) only single port
````
bash <(curl -Ls https://raw.githubusercontent.com/vaxilu/x-ui/master/install.sh)
````

- The rest of the panels are not tested (please test it yourself, if it is ok, let us know so we can add it to the supported panels list)

</details>



<details markdown="4"> <summary><h3>Persian</h3></summary>

<br>

## فهرست

- [فایل htaccess](#فایل-htaccess)
- [خطاهای نصب ربات](#خطاهای-نصب-ربات) 
- [نکات مهم](#نکات-مهم)
- [تنظیمات ربات](#تنظیمات-ربات)
- [پورت اشتراکی](#پورت-اشتراکی)
- [رفع ارور پنل](#رفع-ارور-پنل)
- [پنل های پشتیبانی شده](#پنل-های-پشتیبانی-شده)

<br>


## فایل htaccess

- بعد از Extract کردن فایل های پروژه احتمالا فایل htaccess برای شما قابل دیدن نباشد ابتدا گوشه سمت راست بالا بر روی Settings کلیک کنید
- در پنجره باز شده تیک گزینه Show Hidden Files (dotfiles) را فعال کنید و سپس save را بزنید الان شما میتوانید فایل را ببینید
- اگر با اینکار ردی از فایل htaccess پیدا نشد مجدد آپلود کنید 

<br>



## خطاهای نصب ربات


#### خطای: دیتابیس امکان برقراری اتصال به دیتابیس نیست 

- اطلاعات نام کاربری یا نام یا پسورد دیتابیس اشتباه می باشد و باید اصلاح کنید


#### خطای: ربات باید روی دامنه ی دارای ssl فعال نصب بشه

- دامنه شما ssl ندارد و باید آن را فعال کنید



#### خطای: رباتی با این توکن یافت نشد

- این توکن اشتباه است و باید توکن را به درستی وارد کنید



#### خطای: فایل های مورد نیاز ربات یافت نشد

- ربات نمی تواند فایل createDB.php را پیدا کند از بودن آن داخل پروژه مطمئن باشید سپس نصب کنید 


### خطای: 500 بعد از نصب ربات

<br>
    <a align="center">
        <img src="https://user-images.githubusercontent.com/27927279/230745829-73c323f7-46ff-4680-8f86-25ab1f026734.PNG" />
    </a>     
<br>


##### داخل هاست cpanel روی گزینه Select PHPVersion کلیک کنید و در بخش extension گزینه های زیر را فعال کنید:
- pdo_mysql
- mysqlnd 
- nd_mysqli 
##### گزینه های زیر را غیر فعال کنید:
- mysqli
- nd_pdo_mysql 

##### گزینه زیر اگر به رنگ آبی بود لطفا فعال کنید:

<br>
    <a align="center">
        <img src="https://user-images.githubusercontent.com/27927279/230842783-16f6d1a5-e726-4533-a57b-98cb04fa8dfc.PNG" />
    </a>     
<br>


## نکات مهم

- بعد از نصب لطفا اسم فایل install.php را تغییر دهید تا کسی به ادرس نصب شما دسترسی نداشته باشد
- برای قفل اجباری کانال حتما ربات را مدیر کانال کنید و تمام دسترسی ها را فعال کنید
- قبل از نصب حتما ورژن php روی 7.4 باشد برای تنظیم داخل هاست cpanel روی گزینه PHP Selector کلیک کنید و دامنه مربوط به ربات را تنظیم کنید  
- داخل هاست cpanel روی گزینه Select PHPVersion کلیک کنید و در بخش extension حتما Soap را فعال کنید  
- برای استفاده از درگاه NowPayment حتما باید مبلغ شارژ بالای 3.5 دلار باشد چون پایین تر از 3.5 دلار قابل پرداخت نمی باشد  
- لوکیش هاست یا سرور لینوکس باید خارج از ایران باشد ( چون تلگرام در ایران فیلتر است )
- اگر از پروتکل تروجان استفاده می کنید پنل باید قابلیت ساخت تروجان را داشته باشد در غیر اینصورت پنل شما به مشکل میخورد
- اعتبار اعلان کانفیگ بعد از 2 روز صفر می شود ( یعنی اگر سرویس را تمدید کند اعلان اتمام مجدد فعال می شود )
- اگر حجم سرویس به یک گیگ و زمان به یک روز برسه برای کاربر اعلان ارسال می شود
- اگر کاربر تا 48 ساعت سرویس را تمدید نکند اعلان پاک شدن سرویس برای کاربر ارسال می شود و سرویس پاک می شود
- اگر موقع تنظیم کرون جاب پیام همگانی یا اعلان ارسال نشد ( هنگام تنظیم کرون جاب فقط ادرس دامنه را از داخل command پاک کنید )
- برای ایجاد کانفیگ تست قیمت را 0 قرار دهید ، هر اکانت فقط یک بار میتواند اکانت تست رایگان استفاده کند
- نوع پنل ( سنای و علیرضا ) برای سنایی نسخه 1.1.1 به بالا و علیرضا نسخه 0.4.2 کاربرد دارد
-  نوع پنل ( ساده ) برای نسخه سنایی 1.0.9 و پایین تر ، علیرضا 0.3.2 و پایین تر ، پنل چینی وکسیلو ، نیدوکا کاربرد دارد
-  برای استفاده از HTTP و Header در ربات باید مقدار Header Type رو مقدار http قرار بدید و برای request header هم مقدار Host:domain.ir وارد کنید
-  برای جلوگیری از فروش یک سرور می توانید موجودی سرور را 0 قرار بدید
-  برای تنظیم کانال اعلان درآمد روی آیدی داخل صفحه کلید شیشه ای کلیک کنید و کانال را مجدد تنظیم کنید
-  برای نصب مجدد ربات باید فایل ها رو از اول دانلود کنید ( فایل های قبلی کار نمی کند چون به فایل جدید به نام baseinfo.php دارید )



<br>

## تنظیمات ربات


#### برای ثبت سرور نکات زیر را رعایت کنید:

- از پورتی استفاده کنید که روی هاست این پورت باز باشد ، اگر مطمئن نیستید به پشتیبانی هاستینگ تیکت بدید و درخواست پورت های باز روی سرور را بدید که معمولا پورت 8080 روی اکثر هاستیگ ها باز می باشد
- اگر پنل شما دارای دامنه می باشد حتما باید ssl پنل فعال باشد و آن را با https شروع کنید
- اگر پنل شما دارای ای پی می باشد لطفا تنظیمات خود پنل را طبق تصویر زیر دوتا کادر را پاک کنید و پنل را ذخیره و ریستارت کنید و برای ثبت سرور در ربات باید به صورت http وارد کنید


<a align="center">
    <img src="https://user-images.githubusercontent.com/27927279/228873312-7ac5f12a-5d67-465f-a106-45a11f8f82ee.PNG" />
</a>   


<br>

#### تنظیم پروتکل و شبکه:


دقت کنید اگر تنظیمات tls - xtls رو اینطوری تنظیم می کنید ، هنگام ثبت پلن باید حتما این پروتکل و شبکه رو ایجاد کنید



- vless  `ws - tcp ( tls - xtls )`
- vmess  `ws - tcp ( tls - xtls )`
- vless  `Grpc ( tls )`
- vmess  `Grpc ( tls )`
- trojan  `tcp ( xtls )`





#### هنگام اضافه کردن سرور به ربات لطفا به صورت زیر آدرس وارد کنید




````
https://youdomain.com:54321
````

````
http://192.180.125:54321
````

````
https://youdomain.com:54321/path
````

#### آدرس زیر اشتباه می باشد

````
https://youdomain.com:54321/xui/inbounds
````

````
https://youdomain.com:54321/
````


<br>

#### تنظیم سرتیفیکیت داخل ربات


- tls: `{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}]}`


- xtls: `{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}],"alpn": []}`

مثال: 

- نکته مهم: بین آدرس ssl فاصله نزارید و بدون فاصله باشد در غیر اینصورت کل پنل x-ui از کار می افتد

- serverName: yourdomain.com
- certificateFile: /root/cert.crt
- keyFile: /root/private.key


````
{"serverName": "yourdomain.com","certificates": [{"certificateFile": "/root/cert.crt","keyFile": "/root/private.key"}]}
````

````
{"serverName": "yourdomain.com","certificates": [{"certificateFile": "/root/cert.crt","keyFile": "/root/private.key"}],"alpn": []}
````

<br>


## پورت اشتراکی


- با یک خروجی می توانید به اندازه دلخواه روی دامنه های مختلف کانفیگ تحویل بگیرید ( سابسکرایب برای اپراتورهای مختلف )
- جهت استفاده از پورت خاص یا اشتراکی ابتدا دستی یک کانفیگ با یه پورت خاص ایجاد کنید و در پلن اشتراکی ربات ، id سطر کانفیگ را به ربات بدید تا کانفیگ ها را به صورت خودکار روی پورت ایجاد کند


<br>

## رفع ارور پنل

- اگر با همچین خطایی مواجه شدین لطفا موارد زیر را با دقت انجام بدید


<br>


<p align="center">
    <a>
        <img src="https://user-images.githubusercontent.com/27927279/228843013-e06c3655-1fc9-44aa-a256-30d0d4a9a784.jpg" />
    </a>
</p>


<br>

- ابتدا نرم افزار Navicat یا DB Browser for SQLite را دانلود کنید ( نرم افزار Navicat باید کرک کنید ولی رابط کاربری قشنگی دارد ) 
- فایل دیتابیس پنل x-ui که با فرمت x-ui.db می باشد را از سرور که در مسیر etc/x-ui/x-ui.db است دانلود کنید
- فایل دیتابیس x-ui.db را وارد نرم افزار موردنظر کنید، سپس کانفیگ اخری را حذف کنید و پروژه را ذخیره کنید
- فایل جدید را داخل سرور آپلود کنید و یک بار پنل را ریستارت کنید مشکل برطرف می شود


<br>

## پنل های پشتیبانی شده


- ( نیدوکا کالانکا ) تک پورتی ، چند پورتی ( بهترین گزینه برای ربات )
```` 
bash <(curl -Ls https://raw.githubusercontent.com/NidukaAkalanka/x-ui-english/master/install.sh)
```` 
- ( سنایی ) چند پورتی - تک پورتی
```` 
bash <(curl -Ls https://raw.githubusercontent.com/mhsanaei/3x-ui/master/install.sh)
````   
```` 
bash <(curl -Ls https://raw.githubusercontent.com/mhsanaei/3x-ui/master/install.sh) v1.0.9
```` 
- ( علیرضا ) چند پورتی - تک پورتی
```` 
bash <(curl -Ls https://raw.githubusercontent.com/alireza0/x-ui/master/install.sh)
````   
```` 
bash <(curl -Ls https://raw.githubusercontent.com/alireza0/x-ui/master/install.sh) 0.4.0
```` 
- ( وکسیلو ) فقط تک پورتی
```` 
bash <(curl -Ls https://raw.githubusercontent.com/vaxilu/x-ui/master/install.sh)
```` 

- بقیه پنل ها تست نشدن ( لطفا خودتون تست کنید اگر اوکی بود بگید اینجا اضافه بشه )

</details>

<br>
<br>

## Donation

- Sepe Bank: `5892101222351344`
- Tron (TRX): `TY8j7of18gbMtneB8bbL7SZk5gcntQEemG`
- Bitcoin: `bc1qcnkjnqvs7kyxvlfrns8t4ely7x85dhvz5gqge4`
- Dogecoin: `DMyGMghEh4W55P3VeVHntCN3vYAFtshvVH`

<br>

## WizWiz Facilities

- nowpayments currency portal
- Zarinpal Gate
- Grpc - ws - tcp network support
- xtls support - tls
- Support flow - alpn
- Ability to extend the service
- The ability to create configs with total traffic in megabytes (MB)
- Ability to output config (suitable for different internet providers or subscription)
- The ability to change the protocol (vless-vmess-trojan) by the user
- The ability to change the network type (ws-tcp-grpc) by the user
- Setting the config port randomly or automatically in the robot
- Ability to create multiple accounts by admin
- config delivered to the user with (the user's Telegram numeric ID)
- Wallet (rechargeable)
- Ability to charge the wallet by admin
- Send notification of new member in robot to (admin)
- Sending a message to a newly registered user in the robot
- Show the number of purchases - inventory - name and... to the user
- The ability to send a private message from the admin to the user
- Ability to view statistics of users - servers - plans - categories (admin)
- Ability to view purchased products - Total income (admin)
- The ability to block and release users from the robot
- Add or remove admin professionally
- Display the inventory of servers for admin and user
- Ability to turn off/on (robot)
- Ability to turn off/on (sale)
- Ability to turn off / on (config specifications)
- Ability to turn off/on (card by card)
- Ability to turn off/on (wallet)
- Ability to turn off/on (Zarin Pal port)
- Ability to turn off/on (nowpayments currency)
- Ability to send income report by setting the time in the defined channel
- The ability to send (photos, music, etc.) in public messages
- Ability to edit name - description - price - configuration capacity (servers and plans)
- The ability to receive the sold configurations of each plan separately
- Added a button to return when delivering the config to the client
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
- Notification of completion of volume and configuration time (to the user)
- Forced channel lock
- Senate panel support
- Alireza panel support
- Vexilo panel support
- Niduka panel support
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
<br>

Be sure to join the group and channel and support us

## Contact Developer
💎 Group: https://t.me/wizwizdev
💎 Channel: https://t.me/wizwizch

<br>
<br>

## Stargazers over time

[![Stargazers over time](https://starchart.cc/wizwizdev/wizwizxui-timebot.svg)](https://starchart.cc/wizwizdev/wizwizxui-timebot)