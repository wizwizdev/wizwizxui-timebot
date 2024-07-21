<p align="center">
  <a href="https://github.com/wizwizdev/wizwizxui-timebot" target="_blank" rel="noopener noreferrer"></a>
</p>

<p align="center">
  <a href="./README.md">English</a> / <a href="./README-fa.md">فارسی</a>
</p>

<h1 align="center">Welcome to SimpleWiz</h1>

# Installation

1. First, install the original WizWiz according to the instructions on the main WizWiz page: [WizWiz](https://github.com/wizwizdev/wizwizxui-timebot)

2. Then, run the following commands on your server to replace the files with the ones containing the updated text:

```sh
curl -o /var/www/html/wizwizxui-timebot/settings/messagewizwiz.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/messagewizwiz.php

curl -o /var/www/html/wizwizxui-timebot/settings/subLink.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/subLink.php

curl -o /var/www/html/wizwizxui-timebot/settings/tronChecker.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/tronChecker.php

curl -o /var/www/html/wizwizxui-timebot/settings/values.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/values.php

curl -o /var/www/html/wizwizxui-timebot/settings/warnusers.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/warnusers.php

curl -o /var/www/html/wizwizxui-timebot/bot.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/bot.php

curl -o /var/www/html/wizwizxui-timebot/config.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/config.php

curl -o /var/www/html/wizwizxui-timebot/search.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/search.php
