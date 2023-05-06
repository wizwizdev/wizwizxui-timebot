#!/bin/bash

# Written By: wizwiz

if [ "$(id -u)" -ne 0 ]; then
    echo -e "\033[33mPlease run as root\033[0m"
    exit
fi

wait

echo " "

echo -e "\e[32m
██     ██ ██ ███████ ██     ██ ██ ███████     ██    ██ ██████  ██████   █████  ████████ ███████ 
██     ██ ██    ███  ██     ██ ██    ███      ██    ██ ██   ██ ██   ██ ██   ██    ██    ██      
██  █  ██ ██   ███   ██  █  ██ ██   ███       ██    ██ ██████  ██   ██ ███████    ██    █████   
██ ███ ██ ██  ███    ██ ███ ██ ██  ███        ██    ██ ██      ██   ██ ██   ██    ██    ██      
 ███ ███  ██ ███████  ███ ███  ██ ███████      ██████  ██      ██████  ██   ██    ██    ███████ 
\033[0m\n"


PS3=" Please Select Action: "
options=("Update" "Backup" "Donate" "Exit")
select opt in "${options[@]}"
do
	case $opt in
		"Update")
			echo " "
			read -p "Are you sure you want to update?[y/n]: " answer
			echo " "
			if [ "$answer" != "${answer#[Yy]}" ]; then
			mv /var/www/html/wizwizxui-timebot/baseInfo.php /root/
			echo -e "\n\e[92mUpdating ...\033[0m\n"
			sleep 4
			rm -r /var/www/html/wizwizxui-timebot/
			echo -e "\n\e[92mWait a few seconds ...\033[0m\n"
			sleep 3
			git clone https://github.com/wizwizdev/wizwizxui-timebot.git /var/www/html/wizwizxui-timebot
			sudo chown -R www-data:www-data /var/www/html/wizwizxui-timebot/
			sudo chmod -R 755 /var/www/html/wizwizxui-timebot/
			sleep 3
			mv /root/baseInfo.php /var/www/html/wizwizxui-timebot/
			if [ $? -ne 0 ]; then
			echo -e "\n\e[41mError: The update failed!\033[0m\n"
			exit 1
			else
			echo -e "\n\e[92mThe script was successfully updated!\033[0m\n"
			fi

			else
			  echo -e "\e[41mCancel the update.\033[0m\n"
			fi

			break ;;

		"Backup")
			echo " "
			wait

			printf "\e[33m[+] \e[36mdatabase username: \033[0m"
			read ROOT_USER
			printf "\e[33m[+] \e[36mdatabase password: \033[0m"
			read ROOT_PASSWORD						
			printf "\e[33m[+] \e[36mbot token: \033[0m"
			read BOT_TOKEN
			printf "\e[33m[+] \e[36madmin id: \033[0m"
			read ADMIN_ID
			printf "\e[33m[+] \e[36mSet cron minutes: \033[0m"
			read CRON_TAB
			echo " "
			rm /root/backup-wizwiz.php
			if [ "$BOT_TOKEN" = "" ] || [ "$ADMIN_ID" = "" ] || [ "$CRON_TAB" = "" ] || [ "$ROOT_USER" = "" ] || [ "$ROOT_PASSWORD" = "" ]; then
			exit
			fi
			ASAS="$"

			echo "SELECT 1" | mysql -u$ROOT_USER -p$ROOT_PASSWORD 2>/dev/null

			if [ $? -eq 0 ]; then

			touch backup-wizwiz.php

			chmod -R 777 /root/backup-wizwiz.php

			echo " " >> /root/backup-wizwiz.php
			echo "<?php" >> /root/backup-wizwiz.php
			echo "function sendDocument(${ASAS}username, ${ASAS}document_path, ${ASAS}caption = null, ${ASAS}parse_mode = 'HTML') {" >> /root/backup-wizwiz.php
			echo "${ASAS}url = 'https://api.telegram.org/bot${BOT_TOKEN}/sendDocument';" >> /root/backup-wizwiz.php
			echo "${ASAS}wizwiz = ['chat_id' => ${ASAS}username,'document' => new CURLFile(${ASAS}document_path),'caption' => ${ASAS}caption,'parse_mode' => ${ASAS}parse_mode];" >> /root/backup-wizwiz.php
			echo "${ASAS}ch = curl_init();" >> /root/backup-wizwiz.php
			echo "curl_setopt_array(${ASAS}ch, [CURLOPT_URL => ${ASAS}url,CURLOPT_RETURNTRANSFER => true,CURLOPT_POSTFIELDS => ${ASAS}wizwiz]);" >> /root/backup-wizwiz.php
			echo "${ASAS}result = curl_exec(${ASAS}ch);curl_close(${ASAS}ch);return ${ASAS}result;}" >> /root/backup-wizwiz.php
			echo "date_default_timezone_set('Asia/Tehran');${ASAS}date = date('Y-m-d | H:i:s');" >> /root/backup-wizwiz.php
			echo "sendDocument('${ADMIN_ID}', '/root/wizwiz.sql', '❤️ '.${ASAS}date);" >> /root/backup-wizwiz.php
			echo "?>" >> /root/backup-wizwiz.php
			echo " " >> /root/backup-wizwiz.php

			(crontab -l ; echo "*/${CRON_TAB} * * * * /usr/bin/php /root/backup-wizwiz.php >/dev/null 2>&1") | sort - | uniq - | crontab -


			DB_NAME=wizwiz
			backup_path="/root/"
			backup_filesql="$backup_path$DB_NAME.sql"
			mysqldump --user=$ROOT_USER --password=$ROOT_PASSWORD --host=localhost wizwiz > $backup_filesql
			
			clear
			
			sleep 0.5
			echo -e "\xE2\x9C\x94 \e[92mThe values have been configured\033[0m"
			sleep 0.5
			echo -e "\xE2\x9C\x94 \e[92mThe cron job has been set\033[0m"
			sleep 0.5
			echo -e "\xE2\x9C\x94 \e[92mA new file was created in the root path\033[0m"
			sleep 0.5
			echo -e "\xE2\x9C\x94 \e[92mThe database username and password were correct\033[0m"
			sleep 0.5
			echo -e "\xE2\x9C\x94 \e[92mThe token was registered\033[0m"
			sleep 0.5
			echo -e "\xE2\x9C\x94 \e[92mAdmin's numeric ID was registered\033[0m"
			sleep 0.5
			echo -e "\xE2\x9C\x94 \e[92mSettings saved successfully\033[0m"
			sleep 0.5
			echo -e "\xE2\x98\x85 \e[94mThe backup settings have been successfully completed.\033[0m\n"
			
			else
			    echo "ERROR: MySQL password is incorrect"
			fi

			break ;;
		"Donate")
			echo -e "\n\e[91mTron(trx): \e[36mTY8j7of18gbMtneB8bbL7SZk5gcntQEemG\n\e[91mBitcoin: \e[36mbc1qcnkjnqvs7kyxvlfrns8t4ely7x85dhvz5gqge4\033[0m\n"
			exit 0
			break ;;
		"Exit")
			break
			;;
			*) echo "Invalid option!"
	esac
done
