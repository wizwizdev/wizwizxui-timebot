#!/bin/bash

# Written By: wizwiz

if [ "$(id -u)" -ne 0 ]; then
    echo -e "\033[33mPlease run as root\033[0m"
    exit
fi

wait

echo -e "\e[32m
██     ██ ██ ███████ ██     ██ ██ ███████     ██   ██ ██    ██ ██ 
██     ██ ██    ███  ██     ██ ██    ███       ██ ██  ██    ██ ██ 
██  █  ██ ██   ███   ██  █  ██ ██   ███         ███   ██    ██ ██ 
██ ███ ██ ██  ███    ██ ███ ██ ██  ███         ██ ██  ██    ██ ██ 
 ███ ███  ██ ███████  ███ ███  ██ ███████     ██   ██  ██████  ██ 
\033[0m"
echo -e "    \e[31mTelegram Channel: \e[34m@wizwizch\033[0m | \e[31mTelegram Group: \e[34m@wizwizdev\033[0m\n"

#sleep
echo -e "\e[32mInstalling WizWiz script ... \033[0m\n"
sleep 5

# Update & upgrade
sudo apt update && apt upgrade -y
echo -e "\e[92mThe server was successfully updated ...\033[0m\n"

# Install Git
sudo apt install git -y
echo -e "\n\033[33mThe git was installed successfully\033[0m\n"


PKG=(
    lamp-server^
    libapache2-mod-php 
    mysql-server 
    apache2 
    php-mbstring 
    php-zip 
    php-gd 
    php-json 
    php-curl 
#     phpmyadmin
)

for i in "${PKG[@]}"
do
    dpkg -s $i &> /dev/null
    if [ $? -eq 0 ]; then
        echo "$i is already installed"
    else
        apt install $i -y
        if [ $? -ne 0 ]; then
            echo "Error installing $i"
            exit 1
        fi
    fi
done

echo -e "\n\e[92mPackages Installed Continuing ...\033[0m\n"

echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/app-password-confirm password wizwizhipass' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password wizwizhipass' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password wizwizhipass' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
sudo apt-get install phpmyadmin -y
sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-available/phpmyadmin.conf
sudo a2enconf phpmyadmin.conf
sudo systemctl restart apache2

wait

sudo apt-get install -y php-soap

sudo apt-get install libapache2-mod-php

# extension=soap.so
# echo "extension=soap.so" >> /usr/local/lib/php.ini
# sed -i 's/;extension=soap/extension=soap/g' /usr/local/lib/php.ini


# services
sudo systemctl enable mysql.service
sudo systemctl start mysql.service
sudo systemctl enable apache2
sudo systemctl start apache2

echo -e "\n\e[92m Setting Up UFW...\033[0m\n"

ufw allow 'Apache'

sudo systemctl restart apache2

echo -e "\n\e[92mInstalling ...\033[0m\n"
sleep 1
git clone https://github.com/wizwizdev/wizwizxui-timebot.git /var/www/html/wizwizxui-timebot
sudo chown -R www-data:www-data /var/www/html/wizwizxui-timebot/
sudo chmod -R 755 /var/www/html/wizwizxui-timebot/
echo -e "\n\033[33mWizWiz config and script have been installed successfully\033[0m"

wait

if [ "$(id -u)" -ne 0 ]; then
    echo -e "\033[33mPlease run as root\033[0m\n"
    exit
fi

echo " "
echo -e "\e[32m
██     ██ ██ ███████ ██     ██ ██ ███████     ███████ ███████ ██      
██     ██ ██    ███  ██     ██ ██    ███      ██      ██      ██      
██  █  ██ ██   ███   ██  █  ██ ██   ███       ███████ ███████ ██      
██ ███ ██ ██  ███    ██ ███ ██ ██  ███             ██      ██ ██      
 ███ ███  ██ ███████  ███ ███  ██ ███████     ███████ ███████ ███████ 
\033[0m\n"

read -p "Enter the domain: " domainname
if [ "$domainname" = "" ]; then

echo -e "\n\033[91mPlease wait ...\033[0m\n"
sleep 3

echo -e "\e[36mNothing was registered for the domain.\033[0m\n"

echo -e "\n\033[0m Good Luck Baby\n"

else
# variables
DOMAIN_NAME="$domainname"
# WILDCARD_DOMAIN="*.$wildcarddomain"

# update cron
(crontab -l ; echo "* * * * * curl https://${DOMAIN_NAME}/wizwizxui-timebot/settings/messagewizwiz.php >/dev/null 2>&1") | sort - | uniq - | crontab -
(crontab -l ; echo "* * * * * curl https://${DOMAIN_NAME}/wizwizxui-timebot/settings/rewardReport.php >/dev/null 2>&1") | sort - | uniq - | crontab -
(crontab -l ; echo "* * * * * curl https://${DOMAIN_NAME}/wizwizxui-timebot/settings/warnusers.php >/dev/null 2>&1") | sort - | uniq - | crontab -

echo -e "\n\e[92m Setting Up Cron...\033[0m\n"

# Allow HTTP and HTTPS traffic
echo -e "\n\033[1;7;31mAllowing HTTP and HTTPS traffic...\033[0m\n"
sudo ufw allow 80
sudo ufw allow 443

# Let's Encrypt
echo -e "\n\033[1;7;32mInstalling Let's Encrypt...\033[0m\n"
sudo apt install letsencrypt -y

# automatic certificate renewal
echo -e "\n\033[1;7;33mEnabling automatic certificate renewal...\033[0m\n"
sudo systemctl enable certbot.timer

# SSL certificate using standalone mode
echo -e "\n\033[1;7;34mObtaining SSL certificate using standalone mode...\033[0m\n"
sudo certbot certonly --standalone --agree-tos --preferred-challenges http -d $DOMAIN_NAME

# Certbot Apache plugin
echo -e "\n\033[1;7;35mInstalling Certbot Apache plugin...\033[0m\n"
sudo apt install python3-certbot-apache -y

# SSL certificate using Apache plugin
echo -e "\n\033[1;7;36mObtaining SSL certificate using Apache plugin...\033[0m\n"
sudo certbot --apache --agree-tos --preferred-challenges http -d $DOMAIN_NAME

# SSL certificate using manual DNS mode (wildcard)
# echo -e "\n\033[1;7;33mObtaining SSL certificate using manual DNS mode (wildcard)...\033[0m\n"
# sudo certbot certonly --manual --agree-tos --preferred-challenges dns -d $DOMAIN_NAME -d $WILDCARD_DOMAIN


systemctl restart cron
systemctl restart apache2

echo -e "\e[32m======================================"
echo -e "SSL certificate obtained successfully!"
echo -e "======================================\033[0m"

echo -e "\n\e[91mInstall addres:\033[0m https://\e[92m${domainname}\033[0m/wizwizxui-timebot/install/install.php"
echo -e "\e[91mphpmyadmin addres:\033[0m https://\e[92m${domainname}\033[0m/phpmyadmin\n"

fi
