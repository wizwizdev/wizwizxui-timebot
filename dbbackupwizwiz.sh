#!/bin/bash

telegramBotToken=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$botToken' | cut -d"'" -f2)
telegramBotToken2=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$botToken' | cut -d'"' -f2)
filepath="/var/www/html/wizwizxui-timebot/baseInfo.php"
chatID=$(cat $filepath | grep '$admin =' | sed 's/.*= //' | sed 's/;//')

databaseUser=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbUserName' | cut -d"'" -f2)
databasePassword=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbPassword' | cut -d"'" -f2)
databaseName=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbName' | cut -d"'" -f2)

backupDir='/tmp/db_backup'
mkdir -p $backupDir
backupFilename="wizwiz_$(date +'%Y-%m-%d_%H-%M-%S').sql"
mysqldump -u$databaseUser -p$databasePassword $databaseName > $backupDir/$backupFilename

telegramAPI="https://api.telegram.org/bot$telegramBotToken/sendDocument"
curl -F "chat_id=$chatID" -F "document=@$backupDir/$backupFilename" "$telegramAPI"
rm "$backupDir/$backupFilename"


