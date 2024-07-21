
# Simple Wiz Version

<div id="language-toggle">
  <button onclick="showEnglish()">English</button>
  <button onclick="showPersian()">فارسی</button>
</div>

<div id="english-content">
  This is a version of the WizWiz Bot (https://github.com/wizwizdev/wizwizxui-timebot) with simplified and more formal text, and all emojis removed.

  ## Installation Instructions

  1. First, install the original WizWiz according to the instructions on the main WizWiz page:
     https://github.com/wizwizdev/wizwizxui-timebot

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
  ```

  ## Additional Information

  For more details, visit the original WizWiz repository: https://github.com/wizwizdev/wizwizxui-timebot.
</div>

<div id="persian-content" style="display:none;">
  این یک یک نسخه از ربات ویز ویز ( https://github.com/wizwizdev/wizwizxui-timebot) است با این تفاوت که از متن های ساده و رسمی تر استفاده شده و ایموجی های موجود در ربات به صورت کامل پاک شده

  ## دستورالعمل نصب

  1. اول باید ویز ویز اصلی رو طبق توضیحات صفحه اصلی ویز ویز نصب کنین:
     https://github.com/wizwizdev/wizwizxui-timebot

  2. و بعد با اجرا کردن این دستور در سرور خودتون، فایل هایی که شامل متن های تغییرداده شده هستن با فایل های نسخه اصلی جایگزین میشن:

  ```sh
  curl -o /var/www/html/wizwizxui-timebot/settings/messagewizwiz.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/messagewizwiz.php

  curl -o /var/www/html/wizwizxui-timebot/settings/subLink.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/subLink.php

  curl -o /var/www/html/wizwizxui-timebot/settings/tronChecker.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/tronChecker.php

  curl -o /var/www/html/wizwizxui-timebot/settings/values.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/values.php

  curl -o /var/www/html/wizwizxui-timebot/settings/warnusers.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/warnusers.php

  curl -o /var/www/html/wizwizxui-timebot/bot.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/bot.php

  curl -o /var/www/html/wizwizxui-timebot/config.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/config.php

  curl -o /var/www/html/wizwizxui-timebot/search.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/search.php
  ```

  ## اطلاعات اضافی

  برای جزئیات بیشتر، به مخزن اصلی ویز ویز مراجعه کنید: https://github.com/wizwizdev/wizwizxui-timebot.
</div>

<script>
  function showEnglish() {
    document.getElementById('english-content').style.display = 'block';
    document.getElementById('persian-content').style.display = 'none';
  }

  function showPersian() {
    document.getElementById('english-content').style.display = 'none';
    document.getElementById('persian-content').style.display = 'block';
  }
</script>
```

This code will display two buttons at the top of your README. Clicking the "English" button will show the English content, while clicking the "فارسی" button will show the Persian content. Note that GitHub's markdown renderer does support some HTML and JavaScript, but this functionality may not work perfectly on GitHub's markdown viewer. For a fully functional bilingual readme, you might need to rely on external documentation tools or platforms that support such interactivity better.
