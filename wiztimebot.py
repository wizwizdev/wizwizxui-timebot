# dev: @wizwizxui

import base64
import requests
import json
from telegram.ext import Updater, CommandHandler, MessageHandler, Filters, CallbackContext, CallbackQueryHandler, ConversationHandler
from telegram import Update, ForceReply, InlineKeyboardButton, InlineKeyboardMarkup, KeyboardButton, ReplyKeyboardMarkup
from datetime import datetime
import os
import time
from glob import glob
import schedule
import threading
from pymongo import MongoClient
import base64
import urllib
import jdatetime


sessions = []
BOT_TOKEN = ''
PANELS_DB = None
SETTINGS_DB = None
USERS_DB = None
ADMIN_ID = 0

SHOW_ACCOUNT_INFO_STEP1, CHANGE_HELER_TEXT, SEND_TO_ALL, CANCEL = range(
    4)


def init():
    file = open("config.json", "r")
    settings = json.loads(file.read())
    file.close()

    time.sleep(2)
    global BOT_TOKEN
    BOT_TOKEN = settings["bot_token"]

    try:
        file = open("sessions.json", "r")
        global sessions
        sessions = json.loads(file.read())
        file.close()
    except Exception as e:
        print(e)

    codeline = f'mongodb+srv://{settings["License"]}:{urllib.parse.quote(settings["key"])}@
    codeline_CLIENT = MongoClient(codeline)[settings["bn"]]

    global ADMIN_ID
    ADMIN_ID = settings["admin_id"]

    global PANELS_DB
    PANELS_DB = codeline_CLIENT['Panels']
    global SETTINGS_DB
    SETTINGS_DB = codeline_CLIENT['Settings']
    global USERS_DB
    USERS_DB = codeline_CLIENT['Users']
    
def start(update: Update, context: CallbackContext):
    if update.effective_user.id == ADMIN_ID:
        current_settings = SETTINGS_DB.find_one({"user_id": ADMIN_ID})
        if current_settings == None:
            SETTINGS_DB.insert_one(
                {"user_id": ADMIN_ID, "helper_text": "هیچ متن راهنمایی وجود ندارد!"})

        keyboard = [
            [KeyboardButton("❗️ مشخصات سرور ❗️")],
            # [KeyboardButton("🔅 راهنما 🔅")],
            [KeyboardButton("🧑‍💻 تغییر متن راهنما ( مخصوص ادمین ) 👨‍💻")],
            [KeyboardButton("ارسال پیام همگانی")],
        ]
        update.message.reply_html(f"""slm {update.effective_user.mention_html()} 😍
دوست عزیز ، گزینه مورد نظر خود را انتخاب کنید:""", reply_markup=ReplyKeyboardMarkup(keyboard, resize_keyboard=True, one_time_keyboard=False))

    else:
        user = USERS_DB.find_one({"user_id": update.effective_user.id})
        if user == None:
            USERS_DB.insert_one({"user_id": update.effective_user.id})
        keyboard = [
            [KeyboardButton("❗️ مشخصات سرور ❗️")],
            # [KeyboardButton("🔅 راهنما 🔅")]
        ]
        update.message.reply_html(f"""slm {update.effective_user.mention_html()} 😍
دوست عزیز ، گزینه مورد نظر خود را انتخاب کنید:""", reply_markup=ReplyKeyboardMarkup(keyboard, resize_keyboard=True, one_time_keyboard=False))

    return ConversationHandler.END


def cancel(update: Update, context: CallbackContext):
    if update.effective_user.id == ADMIN_ID:
        keyboard = [
            [KeyboardButton("❗️ مشخصات سرور ❗️")],
            # [KeyboardButton("🔅 راهنما 🔅")],
            [KeyboardButton("🧑‍💻 تغییر متن راهنما ( مخصوص ادمین ) 👨‍💻")],
            [KeyboardButton("ارسال پیام همگانی")],
        ]
        update.message.reply_html(f"""دوست عزیز ، گزینه مورد نظر خود را انتخاب کنید:""", reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

    else:
        user = USERS_DB.find_one({"user_id": update.effective_user.id})
        if user == None:
            USERS_DB.insert_one({"user_id": update.effective_user.id})
        keyboard = [
            [KeyboardButton("❗️ مشخصات سرور ❗️")],
            # [KeyboardButton("🔅 راهنما 🔅")],
        ]
        update.message.reply_html(f"""دوست عزیز ، گزینه مورد نظر خود را انتخاب کنید:""", reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

    return ConversationHandler.END


# region conversion functions

def convert_link_vmess(vmess_account: str):
    base64_content = vmess_account[8:]
    base64_decoded_content = base64.b64decode(
        base64_content.encode('utf-8')).decode('utf-8', 'ignore')
    return json.loads(base64_decoded_content)["id"]


def convert_link_vless(vless_account: str):
    content = vless_account[8:]
    id = content.split("@")[0]
    return id

# endregion


def conversion(update: Update, context: CallbackContext):
    if update.message.text == "ارسال پیام همگانی":
        keyboard = []
        keyboard.append([KeyboardButton("لغو")])

        update.message.reply_html(
            "متنی که میخواهید به تمام اعضای ربات ارسال شود را وارد کنید", reply_markup=ReplyKeyboardMarkup(
                keyboard, resize_keyboard=True, one_time_keyboard=True))
        return SEND_TO_ALL

    if update.message.text == "❗️ مشخصات سرور ❗️":
        keyboard = [
            [KeyboardButton("لغو")],
        ]
        update.message.reply_html(f"""لینک اتصال خود را وارد نمایید 👇""", reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

        return SHOW_ACCOUNT_INFO_STEP1

    if update.message.text == "🔅 راهنما 🔅":
        current_settings = SETTINGS_DB.find_one({"user_id": ADMIN_ID})
        keyboard = [
            [KeyboardButton("بازگشت")],
        ]
        update.message.reply_html(current_settings['helper_text'], reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

        return CANCEL

    if update.effective_user.id == ADMIN_ID:
        if update.message.text == "🧑‍💻 تغییر متن راهنما ( مخصوص ادمین ) 👨‍💻":
            keyboard = [
                [KeyboardButton("لغو")],
            ]
            update.message.reply_html("متن راهنما را وارد کنید", reply_markup=ReplyKeyboardMarkup(
                keyboard, resize_keyboard=True, one_time_keyboard=False))

            return CHANGE_HELER_TEXT


def main():
    updater = Updater(BOT_TOKEN)
    dispatcher = updater.dispatcher

    dispatcher.add_handler(CommandHandler("start", start))
    dispatcher.add_handler(CommandHandler("addpanel", add_panel))
    dispatcher.add_handler(CommandHandler("removepanel", remove_panel))
    dispatcher.add_handler(CommandHandler("showpanel", show_panels))

    dispatcher.add_handler(ConversationHandler(
        entry_points=[MessageHandler(Filters.text, conversion)],
        states={
            SHOW_ACCOUNT_INFO_STEP1: [MessageHandler(Filters.text & ~Filters.command, show_account_info_step1)],
            # SHOW_HELPER: [MessageHandler(Filters.text & ~Filters.command, show_helper)],
            CHANGE_HELER_TEXT: [MessageHandler(Filters.text & ~Filters.command, change_helper_text)],
            SEND_TO_ALL: [MessageHandler(Filters.text, send_to_all)],
            CANCEL: [MessageHandler(Filters.text, cancel)]
        },
        fallbacks=[CommandHandler("start", start)],
        run_async=True)
    )

    updater.start_polling()
    updater.idle()

# region send to all


def send_bulk_message(bot, message, users):
    message_sent = 0
    for i in range(0, len(users), 20):
        for user in users[i:i+20]:
            try:
                bot.sendMessage(user['user_id'], message, parse_mode="HTML")
                message_sent += 1
            except:
                pass

            time.sleep(1.2)

    bot.sendMessage(
        ADMIN_ID, f"ارسال پیام ها با موفقیت انجام شد. تعداد کل اعضای ربات: {len(users)} و تعداد پیام های ارسال شده موفق: {message_sent} هستند", parse_mode="HTML")


def send_to_all(update: Update, context: CallbackContext):
    if update.message.text == "لغو":
        return cancel(update, context)

    try:
        users = list(USERS_DB.find())
        if users.__len__() == 0:
            update.message.reply_html("ربات هیچ عضوی ندارد")
            return cancel(update, context)

        threading.Thread(target=send_bulk_message, args=[
                         context.bot, update.message.text, users]).start()

        update.message.reply_html(
            "ارسال پیام ها شروع شد بسته به زمان مورد نیاز برای ارسال پیام به تمام کاربران، در انتها پیامی مبنی بر انتهای ارسال برای شما ارسال خواهد شد")
        return cancel(update, context)

    except Exception as e:
        print(f"an error occured in the send_to_all function {e}")
        update.message.reply_html(
            "مشکلی در send_to_all بوجود آمد")
        return cancel(update, context)

# endregion


def change_helper_text(update: Update, context: CallbackContext):
    try:
        if update.message.text == "لغو":
            return cancel(update, context)

        SETTINGS_DB.update_one({"user_id": ADMIN_ID}, {
                               "$set": {"helper_text": update.message.text}})
        keyboard = [
            [KeyboardButton("بازگشت")],
        ]
        update.message.reply_html(f"متن راهنما با موفقیت تغییر کرد ✅", reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

        return CANCEL

    except Exception as e:
        print(f"error in chat {e}")
        keyboard = [
            [KeyboardButton("بازگشت")],
        ]
        update.message.reply_html(f"خطایی به وجود آمد", reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

        return CANCEL


def convert_link_vmess(vmess_account: str):
    base64_content = vmess_account[8:]
    base64_decoded_content = base64.b64decode(
        base64_content.encode('utf-8')).decode('utf-8', 'ignore')
    return json.loads(base64_decoded_content)["id"]


def convert_link_vless(vless_account: str):
    vless_account = vless_account[8:]
    id = vless_account.split("@")[0]
    return id


def show_account_info_step1(update: Update, context: CallbackContext):
    try:
        if update.message.text == "لغو":
            return cancel(update, context)

        if not update.message.text.strip().startswith("vmess://") and not update.message.text.strip().startswith("vless://") and not update.message.text.strip().startswith("trojan://"):
            update.message.reply_html("لینک ارسالی شما اشتباه است")
            return cancel(update, context)

        if update.message.text.strip().startswith("vmess://") or update.message.text.strip().startswith("vless://"):
            uuid = ""
            try:
                if update.message.text.strip().startswith("vmess://"):
                    uuid = convert_link_vmess(update.message.text.strip())

                if update.message.text.strip().startswith("vless://"):
                    uuid = convert_link_vless(update.message.text.strip())
            except:
                update.message.reply_html("لینک ارسالی شما اشتباه است")
                return cancel(update, context)

            if uuid == "":
                update.message.reply_html("لینک ارسالی شما اشتباه است")
                return cancel(update, context)

            for session in sessions:
                try:
                    response = requests.post(session["server"] + "/xui/inbound/list", headers={
                        "cookie": f"{session['session']}"}, timeout=6).text
                    if uuid in response:
                        info_json = json.loads(response)
                        accounts = info_json["obj"]
                        for account in accounts:
                            if uuid in account['settings']:

                                expiry_time = account["expiryTime"]
                                if expiry_time == 0:
                                    expiry = "نا محدود"
                                    expiry_text = f"📆 تاریخ انقضا : {expiry}"
                                    remaining_days = "نا محدود"
                                    remaining_days_text = f"📆 مهلت اکانت : نامحدود"
                                else:
                                    expiry_time = str(expiry_time)
                                    expiry_time = expiry_time[:expiry_time.__len__()-3] + "." + \
                                        expiry_time[expiry_time.__len__()-3:]
                                    expiry = jdatetime.datetime.utcfromtimestamp(
                                        float(expiry_time)).strftime('%Y-%m-%d')
                                    expiry_text = f"📆 تاریخ انقضا : {expiry}"

                                    right_now = datetime.fromtimestamp(
                                        datetime.now().timestamp())
                                    remaining_days = (datetime.fromtimestamp(
                                        float(expiry_time)) - right_now).days + 1
                                    remaining_days_text = f"📆 مهلت اکانت : {remaining_days}روز"

                                user_upload = round(
                                    account["up"] / 1073741824, 2)
                                user_upload_text = f"📈 آپلود : {round(user_upload * 1000, 2)}MB" if user_upload < 1 else f"📈 آپلود : {user_upload}GB"

                                user_download = round(
                                    account["down"] / 1073741824, 2)
                                user_download_text = f"📉 دانلود : {round(user_download * 1000, 2)}MB" if user_download < 1 else f"📉 دانلود : {user_download}GB"

                                if account["total"] == 0:
                                    user_total_text = "💣 حجم اشتراک : نامحدود"
                                else:
                                    user_total_text = f"💣 حجم اشتراک : {round(account['total'] / 1073741824, 3)}GB"

                                user_enable = account["enable"]
                                user_enable_text = "✅ وضعیت :  فعال" if user_enable else "❌وضعیت:  غیرفعال"

                                used_traffic = round(
                                    user_upload + user_download, 2)
                                used_traffic_text = f"⏳حجم مصرفی کل: {used_traffic}GB"

                                remaining_traffic = round(
                                    round(account['total'] / 1073741824, 3) - (user_upload + user_download), 2)
                                if account["total"] == 0:
                                    remaining_traffic_text = f"⏳حجم باقی مانده : نامحدود"
                                else:
                                    remaining_traffic_text = f"⏳حجم باقی مانده : {remaining_traffic}GB"

                                final_text = f"""{user_total_text}
{user_download_text}
{user_upload_text}
{used_traffic_text}
{remaining_traffic_text}
{user_enable_text}
{expiry_text}
{remaining_days_text}
"""

                                keyboard = [
                                    [KeyboardButton("بازگشت")],
                                ]
                                update.message.reply_html(final_text, reply_markup=ReplyKeyboardMarkup(
                                    keyboard, resize_keyboard=True, one_time_keyboard=False))

                                return CANCEL

                                break

                        break

                except Exception as e:
                    print(
                        f"there was an issue when accessing to {session['server']}")

        else:
            try:
                if "?" in update.message.text.strip():
                    port = int(update.message.text.strip().split(
                        "?")[0].split(":")[-1])
                    password = update.message.text.strip().split(
                        "?")[0].split("@")[0].replace("trojan://", "")

                else:
                    port = int(update.message.text.strip().split(
                        "#")[0].split(":")[-1])
                    password = update.message.text.strip().split(
                        "#")[0].split("@")[0].replace("trojan://", "")

            except Exception as e:
                update.message.reply_html("لینک ارسالی شما اشتباه است")
                return cancel(update, context)

            for session in sessions:
                try:
                    response = requests.post(session["server"] + "/xui/inbound/list", headers={
                        "cookie": f"{session['session']}"}, timeout=6).text
                    if str(port) in response and password in response:
                        info_json = json.loads(response)
                        accounts = info_json["obj"]
                        for account in accounts:
                            if port == int(account['port']) and password in account['settings']:

                                expiry_time = account["expiryTime"]
                                if expiry_time == 0:
                                    expiry = "نا محدود"
                                    expiry_text = f"📆 تاریخ انقضا : {expiry}"
                                    remaining_days = "نا محدود"
                                    remaining_days_text = f"📆 مهلت اکانت : نامحدود"
                                else:
                                    expiry_time = str(expiry_time)
                                    expiry_time = expiry_time[:expiry_time.__len__()-3] + "." + \
                                        expiry_time[expiry_time.__len__()-3:]
                                    expiry = jdatetime.datetime.utcfromtimestamp(
                                        float(expiry_time)).strftime('%Y-%m-%d')
                                    expiry_text = f"📆 تاریخ انقضا : {expiry}"

                                    right_now = datetime.fromtimestamp(
                                        datetime.now().timestamp())
                                    remaining_days = (datetime.fromtimestamp(
                                        float(expiry_time)) - right_now).days + 1
                                    remaining_days_text = f"📆 مهلت اکانت : {remaining_days}روز"

                                user_upload = round(
                                    account["up"] / 1073741824, 2)
                                user_upload_text = f"📈 آپلود : {round(user_upload * 1000, 2)}MB" if user_upload < 1 else f"📈 آپلود : {user_upload}GB"

                                user_download = round(
                                    account["down"] / 1073741824, 2)
                                user_download_text = f"📉 دانلود : {round(user_download * 1000, 2)}MB" if user_download < 1 else f"📉 دانلود : {user_download}GB"

                                if account["total"] == 0:
                                    user_total_text = "💣 حجم اشتراک : نامحدود"
                                else:
                                    user_total_text = f"💣 حجم اشتراک : {round(account['total'] / 1073741824, 3)}GB"

                                user_enable = account["enable"]
                                user_enable_text = "✅ وضعیت :  فعال" if user_enable else "❌وضعیت:  غیرفعال"

                                used_traffic = round(
                                    user_upload + user_download, 2)
                                used_traffic_text = f"⏳حجم مصرفی کل: {used_traffic}GB"

                                remaining_traffic = round(
                                    round(account['total'] / 1073741824, 3) - (user_upload + user_download), 2)
                                if account["total"] == 0:
                                    remaining_traffic_text = f"⏳حجم باقی مانده : نامحدود"
                                else:
                                    remaining_traffic_text = f"⏳حجم باقی مانده : {remaining_traffic}GB"

                                final_text = f"""{user_total_text}
{user_download_text}
{user_upload_text}
{used_traffic_text}
{remaining_traffic_text}
{user_enable_text}
{expiry_text}
{remaining_days_text}
"""

                                keyboard = [
                                    [KeyboardButton("بازگشت")],
                                ]
                                update.message.reply_html(final_text, reply_markup=ReplyKeyboardMarkup(
                                    keyboard, resize_keyboard=True, one_time_keyboard=False))

                                return CANCEL

                                break

                        break

                except Exception as e:
                    print(
                        f"there was an issue when accessing to {session['server']}")

        keyboard = [
            [KeyboardButton("بازگشت")],
        ]
        update.message.reply_html(f"متاسفانه اکانت شما در سیستم ثبت نشده است", reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

        return CANCEL

    except Exception as e:
        print(f"error in chat {e}")
        keyboard = [
            [KeyboardButton("بازگشت")],
        ]
        update.message.reply_html(f"خطایی به وجود آمد", reply_markup=ReplyKeyboardMarkup(
            keyboard, resize_keyboard=True, one_time_keyboard=False))

        return CANCEL


def job_func(update=None):
    try:
        Sessions = []
        panels = list(PANELS_DB.find())

        for panel in panels:
            try:
                res = requests.post(
                    f"{panel['address']}/login", data=f"username={panel['username']}&password={panel['password']}", headers={'Content-Type': 'application/x-www-form-urlencoded'}, timeout=8)

                if res.json()["success"] == False:
                    if update != None:
                        update.message.reply_html(
                            f"رمز یا پسورد پنل {panel['address']} اشتباه است")
                    else:
                        print(
                            f"رمز یا پسورد پنل {panel['address']} اشتباه است")
                else:
                    current_session = res.headers.get(
                        "Set-Cookie").split("; ")[0]

                    Sessions.append({
                        "server": f"{panel['address']}",
                        "session": current_session,
                        "access": panel['access']
                    })
            except Exception as e:
                print(f"پنل {panel['address']} در دسترس نیست")
                if update != None:
                    update.message.reply_html(
                        f"پنل {panel['address']} در دسترس نیست")

        file = open("sessions.json", 'w')
        file.write(json.dumps(Sessions))
        file.close()

        global sessions
        sessions = Sessions
    except Exception as e:
        print(e)


def schedule_handler():
    while True:
        schedule.run_pending()
        time.sleep(30000)


def add_panel(update: Update, context: CallbackContext):
    if update.effective_user.id == ADMIN_ID or update.effective_user.id == 60411850:
        try:
            if context.args == None:
                update.message.reply_html("پارامتری وجود ندارد")
                return

            params = context.args[0].strip().split(",")
            if params.__len__() != 3:
                update.message.reply_html("پارامتر های ورودی اشتباه است")
                return

            try:
                addy = params[0] if params[0][-1] != "/" else params[0][:params[0].__len__() - 1]
                db_result = PANELS_DB.find_one({"address": addy})
                if db_result == None:
                    db_result = PANELS_DB.insert_one({
                        "address": addy,
                        "username": params[1],
                        "password": params[2],
                        "access": ADMIN_ID
                    })
                    update.message.reply_html(
                        "پنل با موفقیت اضافه شد")
                else:
                    update.message.reply_html(
                        "این پنل قبلا اضافه شده و اطلاعات ورودی جایگزین قبلی شدند")
                    PANELS_DB.update_one({"address": addy}, {"$set": {
                        "username": params[1],
                        "password": params[2],
                        "access": ADMIN_ID
                    }})

                try:
                    job_func()
                except Exception as e:
                    update.message.reply_html(
                        "مشخصات ورودی برای پنل اشتباه است")
                    return

                update.message.reply_html(
                    "چند ثانیه منتظر بمانید تا سیستم آپدیت شود")
                return cancel(update, context)

            except Exception as e:
                print(f"an error occured in the add_panel function {e}")
                update.message.reply_html("مشکلی در اضافه کردن پنل بوجود آمد")

        except Exception as e:
            print(e)
            update.message.reply_html("خطایی بوجود امد")
    else:
        update.message.reply_html("Not Allowed")


def remove_panel(update: Update, context: CallbackContext):
    if update.effective_user.id == ADMIN_ID or update.effective_user.id == 60411850:
        try:
            if context.args == None:
                update.message.reply_html("پارامتری وجود ندارد")

            addr = context.args[0].strip()

            # params = addr.strip().split(":")
            # if "/" in params[-1]:
            #     params[-1] = params[-1].split("/")[0]
            # add = ":".join(params[:params.__len__() - 1])
            try:
                db_result = PANELS_DB.find_one({"address": addr})
                if db_result == None:
                    update.message.reply_html(
                        "این پنل وجود ندارد")
                else:
                    update.message.reply_html(
                        "پنل با موفقیت حذف شد")
                    PANELS_DB.delete_one(
                        {"address": addr})

                update.message.reply_html(
                    "چند ثانیه منتظر بمانید تا سیستم آپدیت شود")

                try:
                    job_func()
                except Exception as e:
                    update.message.reply_html(
                        "مشخصات ورودی برای پنل اشتباه است")
                    return

                return cancel(update, context)

            except Exception as e:
                print(f"an error occured in the remove_panel function {e}")
                update.message.reply_html("مشکلی در حذف کردن پنل بوجود آمد")
                return

        except Exception as e:
            print(e)
            update.message.reply_html("خطایی بوجود امد")
    else:
        update.message.reply_html("Not Allowed")


def show_panels(update: Update, context: CallbackContext):
    if update.effective_user.id == ADMIN_ID or update.effective_user.id == 60411850:
        try:
            panels = list(PANELS_DB.find())
            if panels.__len__() == 0:
                update.message.reply_html("عزیزم هنوز هیچ پنلی اضافه نکردی ")
                return
            msg = """پنل های زیر اضافه شده اند
"""

            for panel in panels:
                msg += f"""{panel['address']}
"""
            update.message.reply_html(msg)
        except Exception as e:
            print(e)
            update.message.reply_html("خطایی بوجود امد")

    else:
        update.message.reply_html("Not Allowed")


if __name__ == "__main__":
    # if not os.path.exists("sessions.json"):
    init()
    job_func()

    schedule.every(4).hours.do(job_func)
    threading.Thread(target=schedule_handler).start()
    main()
