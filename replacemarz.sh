#!/bin/bash

declare -A files=(
  ["/var/www/html/wizwizxui-timebot/settings/messagewizwiz.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/messagewizwiz.php"
  ["/var/www/html/wizwizxui-timebot/settings/subLink.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/subLink.php"
  ["/var/www/html/wizwizxui-timebot/settings/tronChecker.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/tronChecker.php"
  ["/var/www/html/wizwizxui-timebot/settings/values.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/values.php"
  ["/var/www/html/wizwizxui-timebot/settings/warnusers.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/warnusers.php"
  ["/var/www/html/wizwizxui-timebot/bot.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/botmarz.php"
  ["/var/www/html/wizwizxui-timebot/config.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/config.php"
  ["/var/www/html/wizwizxui-timebot/search.php"]="https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/search.php"
)
download_file() {
  local destination=$1
  local url=$2

  if curl -o "$destination" "$url"; then
    echo "Downloaded $url to $destination successfully."
  else
    echo "Error downloading $url to $destination."
  fi
}
for destination in "${!files[@]}"; do
  download_file "$destination" "${files[$destination]}"
done
