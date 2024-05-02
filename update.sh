#!/bin/bash

# Written By: wizwiz

if [ "$(id -u)" -ne 0 ]; then
    echo -e "\033[33mPlease run as root\033[0m"
    exit
fi

wait

echo " "

PS3=" Please Select Action: "
options=("Update bot" "Update panel" "Backup" "Delete" "Donate" "Exit")
select opt in "${options[@]}"
do
	case $opt in
		"Update bot")
			echo " "
			read -p "Are you sure you want to update?[y/n]: " answer
			echo " "
			if [ "$answer" != "${answer#[Yy]}" ]; then
			mv /var/www/html/wizwizxui-timebot/baseInfo.php /root/
			sudo apt-get install -y git
			sudo apt-get install -y wget
			sudo apt-get install -y unzip
			sudo apt install curl -y
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

			sleep 1

   		db_namewizwiz=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbName' | cut -d"'" -f2)
		  db_userwizwiz=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbUserName' | cut -d"'" -f2)
		  db_passwizwiz=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbPassword' | cut -d"'" -f2)
			bot_token=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$botToken' | cut -d"'" -f2)
			bot_token2=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$botToken' | cut -d'"' -f2)
			bot_url=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$botUrl' | cut -d'"' -d"'" -f2)
			
			filepath="/var/www/html/wizwizxui-timebot/baseInfo.php"
			
			bot_value=$(cat $filepath | grep '$admin =' | sed 's/.*= //' | sed 's/;//')
			
                        MESSAGE="🤖 WizWiz robot has been successfully updated! "$'\n\n'"🔻token: <code>${bot_token}</code>"$'\n'"🔻admin: <code>${bot_value}</code> "$'\n'"🔻phpmyadmin: <code>https://domain.com/phpmyadmin</code>"$'\n'"🔹db name: <code>${db_namewizwiz}</code>"$'\n'"🔹db username: <code>${db_userwizwiz}</code>"$'\n'"🔹db password: <code>${db_passwizwiz}</code>"$'\n\n'"📢 @wizwizch "
			
   			curl -s -X POST "https://api.telegram.org/bot${bot_token}/sendMessage" -d chat_id="${bot_value}" -d text="$MESSAGE" -d parse_mode="html"
			
			curl -s -X POST "https://api.telegram.org/bot${bot_token2}/sendMessage" -d chat_id="${bot_value}" -d text="$MESSAGE" -d parse_mode="html"
			
			sleep 1
        
			url="${bot_url}install/install.php?updateBot"
			curl $url

   			url3="${bot_url}install/install.php?updateBot"
			curl $url3

   			echo -e "\n\e[92mUpdating ...\033[0m\n"
      
			sleep 2

   
			sudo rm -r /var/www/html/wizwizxui-timebot/webpanel
			sudo rm -r /var/www/html/wizwizxui-timebot/install
			rm /var/www/html/wizwizxui-timebot/createDB.php
			rm /var/www/html/wizwizxui-timebot/updateShareConfig.php
			rm /var/www/html/wizwizxui-timebot/README.md
			rm /var/www/html/wizwizxui-timebot/README-fa.md
			rm /var/www/html/wizwizxui-timebot/LICENSE
			rm /var/www/html/wizwizxui-timebot/update.sh
			rm /var/www/html/wizwizxui-timebot/wizwiz.sh
  			rm /var/www/html/wizwizxui-timebot/tempCookie.txt
  			rm /var/www/html/wizwizxui-timebot/settings/messagewizwiz.json
			clear
			
			echo -e "\n\e[92mThe script was successfully updated! \033[0m\n"
			
			else
			  echo -e "\e[41mCancel the update.\033[0m\n"
			fi

			break ;;
		
		"Update panel")
			echo " "
			read -p "Are you sure you want to update?[y/n]: " answer
			echo " "
			if [ "$answer" != "${answer#[Yy]}" ]; then
   
			wait
   			cd /var/www/html/ && find . -mindepth 1 -maxdepth 1 ! -name wizwizxui-timebot -type d -exec rm -r {} \;

	 		touch /var/www/html/index.html
    			echo "<!DOCTYPE html><html><head><title>My Website</title></head><body><h1>Hello, world!</h1></body></html>" > /var/www/html/index.html
       
			
			    
			        
			RANDOM_CODE=$(LC_CTYPE=C tr -dc 'a-zA-Z0-9' < /dev/urandom | head -c 40)
			mkdir "/var/www/html/${RANDOM_CODE}"
			echo "Directory created: ${RANDOM_CODE}"
			echo "Folder created successfully!"
			
			 cd /var/www/html/
			 wget -O wizwizpanel.zip https://github.com/wizwizdev/wizwizxui-timebot/releases/download/10.1.2/wizwizpanel.zip
			
			 file_to_transfer="/var/www/html/wizwizpanel.zip"
			 destination_dir=$(find /var/www/html -type d -name "*${RANDOM_CODE}*" -print -quit)
			
			 if [ -z "$destination_dir" ]; then
			   echo "Error: Could not find directory containing 'wiz' in '/var/www/html'"
			   exit 1
			 fi
			
			 mv "$file_to_transfer" "$destination_dir/" && yes | unzip "$destination_dir/wizwizpanel.zip" -d "$destination_dir/" && rm "$destination_dir/wizwizpanel.zip" && sudo chmod -R 755 "$destination_dir/" && sudo chown -R www-data:www-data "$destination_dir/" 
			
			
			wait


			echo -e "\n\e[92mUpdating ...\033[0m\n"
			
			bot_token=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$botToken' | cut -d"'" -f2)
			bot_token2=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$botToken' | cut -d'"' -f2)
			
			filepath="/var/www/html/wizwizxui-timebot/baseInfo.php"
			
			bot_value=$(cat $filepath | grep '$admin =' | sed 's/.*= //' | sed 's/;//')
			
			MESSAGE="🕹 WizWiz panel has been successfully updated!"

			curl -s -X POST "https://api.telegram.org/bot${bot_token}/sendMessage" -d chat_id="${bot_value}" -d text="$MESSAGE"
			curl -s -X POST "https://api.telegram.org/bot${bot_token2}/sendMessage" -d chat_id="${bot_value}" -d text="$MESSAGE"
			
			sleep 1
			
			if [ $? -ne 0 ]; then
			echo -e "\n\e[41mError: The update failed!\033[0m\n"
			exit 1
			else

			clear

			echo -e ' '
			      echo -e "\e[100mwizwiz panel:\033[0m"
			      echo -e "\e[33maddres: \e[36mhttps://domain.com/${RANDOM_CODE}/login.php\033[0m"
			      echo " "
			      echo -e "\e[92mThe script was successfully updated!\033[0m\n"
			fi




			else
			  echo -e "\e[41mCancel the update.\033[0m\n"
			fi

			break ;;
		"Backup")
			echo " "
			wait

			(crontab -l ; echo "0 * * * * ./dbbackupwizwiz.sh") | sort - | uniq - | crontab -
			
			wget https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/dbbackupwizwiz.sh | chmod +x dbbackupwizwiz.sh
			./dbbackupwizwiz.sh
   
			wget https://raw.githubusercontent.com/wizwizdev/wizwizxui-timebot/main/dbbackupwizwiz.sh | chmod +x dbbackupwizwiz.sh
			./dbbackupwizwiz.sh
			
			echo -e "\n\e[92m The backup settings have been successfully completed.\033[0m\n"

			break ;;
		"Delete")
			echo " "
			
			wait
			
			passs=$(cat /root/confwizwiz/dbrootwizwiz.txt | grep '$pass' | cut -d"'" -f2)
   			userrr=$(cat /root/confwizwiz/dbrootwizwiz.txt | grep '$user' | cut -d"'" -f2)
			pathsss=$(cat /root/confwizwiz/dbrootwizwiz.txt | grep '$path' | cut -d"'" -f2)
			pathsss=$(cat /root/confwizwiz/dbrootwizwiz.txt | grep '$path' | cut -d"'" -f2)
			passsword=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbPassword' | cut -d"'" -f2)
   			userrrname=$(cat /var/www/html/wizwizxui-timebot/baseInfo.php | grep '$dbUserName' | cut -d"'" -f2)
			
			mysql -u $userrr -p$passs -e "DROP DATABASE wizwiz;" -e "DROP USER '$userrrname'@'localhost';" -e "DROP USER '$userrrname'@'%';"

			sudo rm -r /var/www/html/wizpanel${pathsss}
			sudo rm -r /var/www/html/wizwizxui-timebot
			
			clear
			
			sleep 1
			
			(crontab -l | grep -v "messagewizwiz.php") | crontab -
			(crontab -l | grep -v "rewardReport.php") | crontab -
			(crontab -l | grep -v "warnusers.php") | crontab -
			(crontab -l | grep -v "backupnutif.php") | crontab -
			
			echo -e "\n\e[92m Removed successfully.\033[0m\n"
			break ;;
		"Donate")
			echo " "
			echo -e "\n\e[91mBanksaderat ( nosrat ): \e[36m6037691526973185\033[0m\n\e[91mTron(trx): \e[36mTY8j7of18gbMtneB8bbL7SZk5gcntQEemG\n\e[91mBitcoin: \e[36mbc1qcnkjnqvs7kyxvlfrns8t4ely7x85dhvz5gqge4\033[0m\n"
			exit 0
			break ;;
		"Exit")
			echo " "
			break
			;;
			*) echo "Invalid option!"
	esac
done
