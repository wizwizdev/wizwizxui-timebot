const TelegramBot = require('node-telegram-bot-api');

const token = process.env.TELEGRAM_BOT_TOKEN;

if (!token) {
  console.error("Telegram Bot Token not found in environment variables!");
  // process.exit(1); // در صورت نیاز، برنامه را متوقف کنید
}

// Create a bot that uses 'polling' to fetch new updates
// یا می‌توانید از وب‌هوک استفاده کنید:
// const bot = new TelegramBot(token);
// bot.setWebHook(`${process.env.APP_URL}/api/telegram/webhook`);

const bot = new TelegramBot(token, { polling: true }); // فعلاً از polling برای تست اولیه استفاده می‌کنیم

bot.on('polling_error', (error) => {
  console.error(`Polling error: ${error.code} - ${error.message}`);
});

console.log('Telegram bot initialized...');

module.exports = bot;
