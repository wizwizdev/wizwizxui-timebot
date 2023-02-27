## ربات نمایش حجم و زمان اشتراک پنل x-ui

<br>

 در مرحله اول باید یک دیتابیس ایجاد کنیم، برای ایجاد دیتابیس در سایت mongodb.com ثبت نام کنید 

به بخش Database بروید

سپس روی گزینه Build a Database کلیک کنید و یک دیتابیس از لوکیش المان ایجاد کنید و در اخر از شما یه یوزر و یه پسورد برای دیتابیس میخواد که باید انتخاب کنید و یه جا ذخیره کنید که جلوتر نیاز داریم


<br>

https://user-images.githubusercontent.com/27927279/221428162-4bfb5a68-30d9-4b50-96fd-399aacc44fd1.mp4

<br>

بعد از ایجاد شدن دیتابیس روی connect کلیک کنید 

سپس روی گزینه Connect your application کلیک کنید

لینکی که ایجاد شد مخصوص شماست و از @ به بعد رو کپی کنید و یه جا ذخیره کنید برای مثال 
```sh
wiz3.ghdrss.mongodb.net/?retryWrites=true&w=majority
```

<br>

https://user-images.githubusercontent.com/27927279/221428430-c00add6c-100b-4a38-b5e5-665ceef3b65a.mp4

<br>

<br>

داخل فهرست پنل روی گزینه Network Access کلیک کنید سپس روی گزینه   ADD IP ADDRESS کلیک کنید در پنجره باز شده روی گزینه  Allow Access from Anywhere کلیک کنید و سپس روی Confirm کلیک کنید

<br>

https://user-images.githubusercontent.com/27927279/221434942-5d4e0122-aa1c-4a7e-a020-7999a441ccc2.mp4

<br>


 در پنل mongodb سمت چپ داخل فهرست ها روی گزینه Database Access کلیک میکنیم و اینجا ما یک دیتابیس داریم و username ایجاد شده رو کپی میکنیم ، موقع ایجاد دیتابیس اگر یادتون باشه یه password داشتیم ، باید username و password رو کپی کنیم و یه جا ذخیره کنیم
 

### مراحل نصب | دستورات زیر را داخل سرور لینوکسی به ترتیب وارد کنید :


```sh
apt update && apt upgrade -y
```
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

### با دستور زیر توکن و ایدی عددی ( مدیر ) را جایگزین کنید و سپس ذخیره کنید: 

```sh
nano config.json
```

به جای Token باید توکنی که از ربات Botfather دریافت کردید جایگزین کنید
به جای idadmin باید ایدی عددی خودتون رو از ربات  username_to_id_bot دریافت کنید و جایگزین کنید
به جای License باید username ، به جای key باید password و به جای bn باید اسم دیتابیس را که داخل سایت mongodb ایجاد کردید وارد کنید 

<br>

![غعهعخ](https://user-images.githubusercontent.com/27927279/221432931-7ad4095d-0d3d-463d-9055-fab112421f4b.JPG)

<br>

### با دستور زیر فایل timebot.py را ویرایش کنید

```sh
nano wiztimebot.py
```

به خط 46 برید و لینک مخصوص خودتون رو که از سایت mongodb که کپی کردید از بین @ و ' جایگزین کنید و سپس ذخیره کنید


### و در اخر کد زیر را اجرا کنید 

```sh
nohup python3 wiztimebot.py > serverlog.txt 2>&1 &
```

### پنل های زیر را ساپورت می کند:
```sh
FranzKafkaYu
vaxilu
NidukaAkalanka
hossinasaadi
HamedAp
```
نکته مهم: یوزرهایی که یک دارای یک پورت هستند پشتیبانی نمیکند و هر یوزر باید پورت مخصوص به خودش را داشته باشد

<br>

وارد ربات بشید و ربات را  start کنید


### برای اضافه کردن سرور به ربات از دستور زیر استفاده کنید


```sh
/addpanel address/path,user,pass 
```

or

```sh
/addpanel address,user,pass 
```
مثال
```sh
/addpanel http://22.33.333.16:54321,admin,admin
```
```sh
/addpanel https://google.com:54321,admin,admin
```

### برای حذف کردن
```sh
/removepanel addres or /removepanel address/path,user,pass
```
مثال
```sh
/removepanel http://22.33.333.16:54321
```
```sh
/removepanel https://google.com:54321
```

### دیدن پنل های اضافه شده
```sh
/showpanel
```

حتما داخل گروه جوین شین که کلی ربات دیگه قراره به صورت رایکان در اختیار شما قرار بگیره 👇

## Contact Developer
💎 Group: https://t.me/wizwizdev
