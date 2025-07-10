const db = require('../../config/db');
const logger = require('../../utils/logger');

async function findOrCreateUser({ userId, username, firstName }) {
  const connection = await db.getConnection();
  try {
    const [rows] = await connection.execute('SELECT * FROM `users` WHERE `userid` = ?', [userId]);
    if (rows.length === 0) {
      const currentTime = Math.floor(Date.now() / 1000);
      const defaultStep = 'none';
      const defaultWallet = 0;
      const defaultRefCode = 0; // Or generate one based on PHP logic if necessary
      const defaultIsAdmin = false; // Default to not admin
      const defaultIsAgent = 0;     // Default to not an agent

      // Ensure all required columns (based on PHP's users table) have a default or are nullable
      await connection.execute(
        'INSERT INTO `users` (`userid`, `name`, `username`, `refcode`, `wallet`, `date`, `step`, `isAdmin`, `is_agent`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [userId, firstName || null, username || null, defaultRefCode, defaultWallet, currentTime, defaultStep, defaultIsAdmin, defaultIsAgent]
      );
      logger.info(`New user created: ${userId} - ${username || firstName}`);
      // Fetch the newly created user to return consistent data structure
      const [newRows] = await connection.execute('SELECT * FROM `users` WHERE `userid` = ?', [userId]);
      if (newRows.length > 0) {
        return { ...newRows[0], isNew: true };
      } else {
        // This case should ideally not happen if insert was successful
        logger.error(`Failed to fetch newly created user: ${userId}`);
        return null; // Or throw an error
      }
    }
    logger.info(`User found: ${userId} - ${rows[0].username || rows[0].name}`);
    return { ...rows[0], isNew: false };
  } catch (error) {
    logger.error(`Error finding or creating user ${userId}: ${error.message} \nStack: ${error.stack}`);
    throw error;
  } finally {
    if (connection) connection.release();
  }
}

async function findUserById(userId) {
  const connection = await db.getConnection();
  try {
    const [rows] = await connection.execute('SELECT * FROM `users` WHERE `userid` = ?', [userId]);
    if (rows.length > 0) {
      return rows[0];
    }
    logger.warn(`User not found by ID: ${userId}`);
    return null;
  } catch (error) {
    logger.error(`Error finding user by ID ${userId}: ${error.message}`);
    throw error;
  } finally {
    if (connection) connection.release();
  }
}


async function getBotSettings() {
  const connection = await db.getConnection();
  try {
    const [rows] = await connection.execute("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
    if (rows.length > 0 && rows[0].value) {
      try {
        return JSON.parse(rows[0].value);
      } catch (parseError) {
        logger.error('Error parsing BOT_STATES JSON:', parseError, 'Raw value:', rows[0].value);
        return {}; // Return default on parse error
      }
    }
    logger.warn('BOT_STATES not found in settings table or has no value.');
    return { // Sensible defaults if not found, mirroring PHP's `else $botState = array();`
        agencyState: "off",
        sellState: "on", // Assuming sell is on by default if not set
        testAccount: "off",
        sharedExistence: "off",
        individualExistence: "off",
        searchState: "off",
        // ... other defaults from PHP's $botState if not set
    };
  } catch (error) {
    logger.error('Error fetching BOT_STATES:', error);
    return {}; // Return default on DB error
  } finally {
    if (connection) connection.release();
  }
}

async function getButtonValues() {
  // In PHP, these are hardcoded in settings/values.php
  // For Node.js, it's better to manage them in a config file or a dedicated service
  // For now, returning a direct JS object similar to the PHP structure.
  return {
    'bot_reports': "📉 آمار کلی ربات",
    'message_to_user': "📞 پیام خصوصی",
    'user_reports': "🔑 اطلاعات کاربر",
    'admins_list': "👤 لیست ادمین ها",
    'increase_wallet': "💵 افزایش موجودی",
    'decrease_wallet': "💸 کاهش موجودی",
    'create_account': "⌨️ ایجاد اکانت انبوه",
    'ban_user': "❌ مسدود کردن کاربر",
    'unban_user': "✅ آزاد کردن کاربر",
    'server_settings': '🚦مدیریت و تنظیمات سرورها',
    'categories_settings': '🗂 مدیریت دسته ها',
    'plan_settings': '🪣 مدیریت پلن ها',
    'discount_settings': "🎁 مدیریت تخفیف ها",
    'main_button_settings': "🕹 مدیریت دکمه ها ",
    'gateways_settings': '💳 تنظیمات درگاه و کانال',
    'bot_settings': '⚙️ تنظیمات ربات',
    'tickets_list': '📪 تیکت ها',
    'message_to_all': "📨 ارسال پیام همگانی",
    'forward_to_all': "📨 فروارد پیام همگانی",
    'back_to_main': '⤵️ برگرد به منوی اصلی ',
    'cart_to_cart': "💳 کارت به کارت",
    'now_payment_gateway': "💳 درگاه NowPayment",
    'zarinpal_gateway': "💳 درگاه زرین پال",
    'nextpay_gateway': "💳 درگاه نکست پی",
    'weswap_gateway': "💳 درگاه ارزی ریالی",
    'approve': 'تایید ✅',
    'approved': 'تایید شد',
    'decline': 'عدم تایید ❌',
    'declined': 'رد شد',
    'back_button': "برگشت 🔙",
    'renew_connection_link': "🚷 قطع دسترسی و لینک جدید",
    'update_config_connection': "⌛️ بروزرسانی کانفیگ",
    'increase_config_volume': "🩸 افزایش حجم سرویس",
    'increase_config_days': "📆 افزایش زمان سرویس",
    'renew_config': '♻ تمدید سرویس',
    'change_config_location': '🌎 تغییر لوکیشن',
    'selected_protocol': "🚦 پروتکل انتخابی",
    'volume_left': "⏳ حجم باقیمانده:",
    'expire_date': "⏰  تاریخ انقضاء: ",
    'buy_date': "⏰  تاریخ خرید: ",
    'plan_name': " 🚀 نام پلن:",
    'on': "روشن ✅",
    'off': "خاموش ❌",
    'active': "فعال 🟢",
    'deactive': "غیر فعال 🔴",
    'send_phone_number': '☎️ ارسال شماره',
    'send_message_to_user': "✉️ ارسال پیام به کاربر ",
    'cancel': '😪 منصرف شدم بیخیال',
    'join_channel': "عضویت در کانال",
    'have_joined': "عضو شدم ✅",
    'gift_volume_day': "🎯 هدیه حجم و زمان",
    'test_account': "🎁 دریافت اکانت تست ",
    'invite_friends': "🏆 زیر مجموعه گیری",
    'my_info': "🧑‍💼 حساب کاربری",
    'my_subscriptions': '📱 کانفیگ های من',
    'buy_subscriptions': '🛒  خرید کانفیگ جدید',
    'shared_existence': "❕ موجودی اشتراکی ",
    'individual_existence': "❗️ موجودی اختصاصی ",
    'application_links': '🧩 آموزش اتصال',
    'my_tickets': "📨 تیکت های من",
    'search_config': "🪫 مشخصات کانفیگ",
    'delete_config': "❌ حذف کانفیگ",
    'pay_with_wallet': "💰پرداخت با موجودی",
    'request_agency': "🧑‍💼 درخواست نمایندگی 🧑‍💼",
    'agency_setting': "➰ پنل همکاری ➰",
    'agent_one_buy': "➕ خرید تکی",
    'agent_much_buy': "♾ خرید انبوه",
    'agent_bought_accounts': "تعداد خرید",
    'agent_joined_date': "تاریخ عضویت",
    'agent_agency_date': "تاریخ نمایندگی",
    "agent_list": " 📍 مدیریت نمایندگان 📍",
    'search_agent_config': "جستجوی کانفیگ",
    'search_admin_config': "جستجوی کانفیگ کاربر",
    'sharj': "✅ 💳 ارسال رسید - شارژ کیف پول",
    "qr_config": "🔳 کیو آر کانفیگ",
    "qr_sub": "🔳 کیو آر ساب",
    'start_bot': "شروع ربات",
    'enable_config': "فعال سازی کانفیگ",
    'disable_config': "غیر فعال سازی کانفیگ",
    "tron_gateway": "درگاه ترون",
    'plan_discount': "روی پلن",
    'server_discount': "روی سرور",
    'agent_configs_list': '📱 کانفیگ های من' // Added as it was used in PHP logic for agents
  };
}

async function getUserStep(userId) {
  const connection = await db.getConnection();
  try {
    const [rows] = await connection.execute('SELECT step FROM users WHERE userid = ?', [userId]);
    if (rows.length > 0) {
      return rows[0].step;
    }
    logger.warn(`Step not found for user: ${userId}. Returning 'none'.`);
    return 'none';
  } catch (error) {
    logger.error(`Error getting user step for ${userId}: ${error.message}`);
    throw error;
  } finally {
    if (connection) connection.release();
  }
}

async function setUserStep(userId, step) {
  const connection = await db.getConnection();
  try {
    const [result] = await connection.execute('UPDATE users SET step = ? WHERE userid = ?', [step, userId]);
    if (result.affectedRows > 0) {
      logger.info(`User ${userId} step set to: ${step}`);
    } else {
      logger.warn(`Attempted to set step for non-existent user or step was already ${step} for user ID: ${userId}`);
    }
  } catch (error) {
    logger.error(`Error setting user step for ${userId}: ${error.message}`);
    throw error;
  } finally {
    if (connection) connection.release();
  }
}


module.exports = {
  findOrCreateUser,
  findUserById,
  getBotSettings,
  getButtonValues,
  getUserStep,
  setUserStep,
};
