
# ساده‌ویز: نسخه ساده‌شده ربات ویزویز

این یک نسخه اصلاح‌شده از ربات ویزویز ( <https://github.com/wizwizdev/wizwizxui-timebot> ) است که در آن از متن‌های ساده‌تر و رسمی‌تر استفاده شده و تمام ایموجی‌ها حذف گردیده‌اند.

## نصب

برای نصب این نسخه، ابتدا باید ربات ویزویز اصلی را طبق راهنمای موجود در صفحه اصلی آن نصب کنید:

<https://github.com/wizwizdev/wizwizxui-timebot>

پس از نصب ویزویز اصلی، با اجرای دستورات زیر در سرور خود، فایل‌های حاوی متن‌های تغییر یافته را جایگزین فایل‌های نسخه اصلی کنید:

```bash
curl -o /var/www/html/wizwizxui-timebot/settings/messagewizwiz.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/messagewizwiz.php
curl -o /var/www/html/wizwizxui-timebot/settings/subLink.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/subLink.php
curl -o /var/www/html/wizwizxui-timebot/settings/tronChecker.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/tronChecker.php
curl -o /var/www/html/wizwizxui-timebot/settings/values.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/values.php
curl -o /var/www/html/wizwizxui-timebot/settings/warnusers.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/warnusers.php
curl -o /var/www/html/wizwizxui-timebot/bot.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/bot.php
curl -o /var/www/html/wizwizxui-timebot/config.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/config.php
curl -o /var/www/html/wizwizxui-timebot/search.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/search.php
```

<button onclick="changeLanguage()">نمایش به زبان انگلیسی</button>

<script>
function changeLanguage() {
  const markdownText = document.querySelector("markdown-body").innerText;
  const translatedText = "This is a fork of the WizWiz bot ( <https://github.com/wizwizdev/wizwizxui-timebot> ) with simplified and more formal text, and all emojis have been removed.\n\n## Installation\n\nTo install this version, first install the original WizWiz bot by following the instructions on its main page:\n\n<https://github.com/wizwizdev/wizwizxui-timebot>\n\nAfter installing the original WizWiz, run the following commands on your server to replace the files with the modified text files:\n\n```bash\ncurl -o /var/www/html/wizwizxui-timebot/settings/messagewizwiz.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/messagewizwiz.php\ncurl -o /var/www/html/wizwizxui-timebot/settings/subLink.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/subLink.php\ncurl -o /var/www/html/wizwizxui-timebot/settings/tronChecker.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/tronChecker.php\ncurl -o /var/www/html/wizwizxui-timebot/settings/values.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/values.php\ncurl -o /var/www/html/wizwizxui-timebot/settings/warnusers.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/settings/warnusers.php\ncurl -o /var/www/html/wizwizxui-timebot/bot.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/bot.php\ncurl -o /var/www/html/wizwizxui-timebot/config.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/config.php\ncurl -o /var/www/html/wizwizxui-timebot/search.php https://raw.githubusercontent.com/ItsOrv/simple-wiz/main/search.php\n```";
  document.querySelector("markdown-body").innerText = translatedText;
}
</script>

امیدوارم این پاسخ برای شما مفید باشد!
