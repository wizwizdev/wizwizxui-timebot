// src/app.js
require('dotenv').config({ path: __dirname + '/../.env' });
const express = require('express');
const bot = require('./config/telegram'); // Import the initialized bot
const telegramController = require('./api/controllers/telegram.controller');
const paymentApiRoutes = require('./api/routes/payment.routes'); // Assuming you'll create this
const subLinkRoutes = require('./api/routes/subLink.routes'); // Assuming you'll create this


const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Telegram bot listeners (if using polling)
bot.on('message', async (msg) => {
  // console.log("Raw message received by bot.on('message'):", msg);
  await telegramController.handleUpdate({ message: msg });
});

bot.on('callback_query', async (callbackQuery) => {
  // console.log("Raw callback_query received by bot.on('callback_query'):", callbackQuery);
  await telegramController.handleUpdate({ callback_query: callbackQuery });
});

// HTTP Routes
// app.use('/api/telegram', telegramApiRoutes); // If you decide to use webhook for Telegram later
app.use('/pay', paymentApiRoutes); // For payment gateway interactions
app.use('/settings', subLinkRoutes); // For subscription link

app.get('/', (req, res) => {
  res.send('Node.js backend is running and bot is polling!');
});

app.use((err, req, res, next) => {
  console.error("Global error handler:", err);
  res.status(500).send('Something broke!');
});

app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
  console.log('Telegram bot is in polling mode.');
  if (!process.env.TELEGRAM_BOT_TOKEN) {
    console.warn("TELEGRAM_BOT_TOKEN is not set in .env file!");
  }
  if (!process.env.DB_HOST) {
    console.warn("DB_HOST is not set in .env file!");
  }
});
