## Bot showing the volume and remaining subscription x-ui panel

[ترجمه به فارسی](README-persian.md)


<br>

  In the first step, we need to create a database, to create a database, register at mongodb.com

Go to the Database section

Then click on the Build a Database option and create a database from the location of the element, and at the end, it will ask you for a user and a password for the database, which you must choose and save somewhere that we will need later.

 
<br>

https://user-images.githubusercontent.com/27927279/221428162-4bfb5a68-30d9-4b50-96fd-399aacc44fd1.mp4

<br>

After the database is created, click on connect

Then click on Connect your application option

The link that was created is for you and copy from @ onwards and save it somewhere, for example
```sh
wiz3.ghdrss.mongodb.net/?retryWrites=true&w=majority
```

<br>

https://user-images.githubusercontent.com/27927279/221428430-c00add6c-100b-4a38-b5e5-665ceef3b65a.mp4

<br>

<br>

In the list of the panel, click on the Network Access option, then click on the ADD IP ADDRESS option, in the opened window, click on the Allow Access from Anywhere option, and then click on Confirm.

<br>

https://user-images.githubusercontent.com/27927279/221434942-5d4e0122-aa1c-4a7e-a020-7999a441ccc2.mp4

<br>


  In the mongodb panel on the left side inside the lists, we click on the Database Access option and here we have a database and copy the created username, if you remember we had a password when creating the database, we must copy the username and password and save it somewhere.
 

###Installation steps Enter the following commands in the Linux server in order:


```sh
apt install python3-pip -y
```
```sh
git clone https://github.com/wizwizdev/wizwizxui-timebot.git
```
```sh
cd wizwizxui-timebot
```
```sh
pip install -r requirements.txt
```

###Replace the token and numerical id (administrator) with the following command and then save:

```sh
nano config.json
```

Instead of Token, you need to replace the token you received from Botfather bot
Instead of idadmin, you should get your numeric ID from username_to_id_bot and replace it
instead of license, we should enter username, instead of key, we should enter password, and instead of bn, we should enter the name of the database that we created in the mongodb site.

<br>

###Edit the timebot.py file with the following command

```sh
nano wiztimebot.py
```

Go to line 46 and replace your special link that you copied from the mongodb site between @ and ' and then save.


###and finally run the following code

```sh
nohup python3 wiztimebot.py > serverlog.txt 2>&1 &
```

###It supports the following panels:
```sh
FranzKafkaYu
Vaxilu
NidukaAkalanka
hossinasaadi
HamedAp
```
Important note: It does not support users who have one port and each user must have their own port

<br>

Enter the robot and start the robot


###Use the following command to add a server to the robot


```sh
/addpanel address/path,user,pass
```

or

```sh
/addpanel address,user,pass
```
Example
```sh
/addpanel http://22.33.333.16:54321,admin,admin
```
```sh
/addpanel https://google.com:54321,admin,admin
```

###to delete
```sh
/removepanel addres or /removepanel address/path,user,pass
```
Example
```sh
/removepanel http://22.33.333.16:54321
```
```sh
/removepanel https://google.com:54321
```

###View added panels
```sh
/showpanel
```

Be sure to join the group because another robot is going to be available to you in the form of Raikan

## Contact Developer
💎 Group: https://t.me/wizwizdev
