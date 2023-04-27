#!/bin/bash

# Written By: wizwiz

if [ "$(id -u)" -ne 0 ]; then
    echo -e "\033[33mPlease run as root\033[0m"
    exit
fi

wait

echo " "

read -p "Enter your root password: " ROOT_PASSWORD
# read -p "Enter your root : " ROOT_USER
ROOT_USER="root"
echo "SELECT 1" | mysql -u$ROOT_USER -p$ROOT_PASSWORD 2>/dev/null


if [ $? -eq 0 ]; then

wait

    randomdbpass=$(openssl rand -base64 10 | tr -dc 'a-zA-Z0-9' | cut -c1-8)

    randomdbdb=$(openssl rand -base64 10 | tr -dc 'a-zA-Z' | cut -c1-8)

    if [[ $(mysql -u root -p$ROOT_PASSWORD -e "SHOW DATABASES LIKE 'wizwiz'") ]]; then
        clear
        echo -e "\n\e[91mYou have already created the database\033[0m\n"
    else
        dbname=wizwiz
        clear
        echo -e "\n\e[32mPlease enter the database username!\033[0m"
        printf "[+] Default user name is \e[91m${randomdbdb}\e[0m ( let it blank to use this user name ): "
        read dbuser
        if [ "$dbuser" = "" ]; then
        dbuser=$randomdbdb
        fi

        echo -e "\n\e[32mPlease enter the database password!\033[0m"
        printf "[+] Default user name is \e[91m${randomdbpass}\e[0m ( let it blank to use this user name ): "
        read dbpass
        if [ "$dbpass" = "" ]; then
        dbpass=$randomdbpass
        fi

        mysql -u root -p$ROOT_PASSWORD -e "CREATE DATABASE $dbname;" -e "CREATE USER '$dbuser'@'%' IDENTIFIED WITH mysql_native_password BY '$dbpass';GRANT ALL PRIVILEGES ON * . * TO '$dbuser'@'%';FLUSH PRIVILEGES;" -e "CREATE USER '$dbuser'@'localhost' IDENTIFIED WITH mysql_native_password BY '$dbpass';GRANT ALL PRIVILEGES ON * . * TO '$dbuser'@'localhost';FLUSH PRIVILEGES;"
        echo -e "\n\e[95mDatabase Created Cotinuing...\033[0m"
        
        sleep 2
        
        # Database
        echo -e "\n\e[100mDatabase information:\033[0m"
        echo -e "\e[33mDatabase name: \e[36m${dbname}\033[0m" 
        echo -e "\e[33mDatabase username: \e[36m${dbuser}\033[0m"
        echo -e "\e[33mDatabase password: \e[36m${dbpass}\033[0m\n"

        wait
        
        echo -e "Good Luck Baby! \e[94mThis project is for free. If you like it, be sure to donate me :) , so let's go \033[0m\n"

    fi


elif [ "$ROOT_PASSWORD" = "" ] || [ "$ROOT_USER" = "" ]; then
echo -e "\n\e[36mThe password is empty.\033[0m\n"
else 
# Install addres
echo -e "\n\e[36mThe password is not correct.\033[0m\n"
  
fi
