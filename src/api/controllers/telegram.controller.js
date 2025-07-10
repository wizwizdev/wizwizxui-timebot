const telegramService = require('../services/telegramService');
const userService = require('../services/userService');
const logger = require('../../utils/logger');

exports.handleUpdate = async (update) => {
  logger.info('Raw Update:', JSON.stringify(update, null, 2));

  try {
    let userId, chatId, firstName, username, textData, messageId, callbackQueryId;

    if (update.message) {
      const message = update.message;
      chatId = message.chat.id;
      textData = message.text;
      userId = message.from.id;
      firstName = message.from.first_name;
      username = message.from.username; // Might be undefined
      messageId = message.message_id;

      logger.info(`Message from ${userId} (${username || firstName}): "${textData}" in chat ${chatId}`);

      const user = await userService.findOrCreateUser({ userId, username, firstName });
      if (!user) {
        logger.error(`User could not be found or created for ID: ${userId}`);
        await telegramService.sendMessage(chatId, "متاسفانه مشکلی در پردازش اطلاعات شما پیش آمده است.");
        return;
      }

      // TODO: Implement banned user check, spam check, join channel logic from PHP's config.php

      if (textData && textData.toLowerCase().startsWith('/start')) {
        const botState = await userService.getBotSettings();
        const buttonValues = await userService.getButtonValues(); // Ensure this returns a valid object
        const mainMenuText = `سلام ${firstName || 'کاربر گرامی'}! به ربات ما خوش آمدید.`; // Simplified from mainValues['start_message']

        // Check if user is admin (example, replace with actual admin check)
        user.isAdmin = (String(userId) === process.env.TELEGRAM_ADMIN_CHAT_ID);

        await telegramService.sendMainMenu(chatId, mainMenuText, user, botState, buttonValues);
      }
      // ... other commands
    } else if (update.callback_query) {
      const callbackQuery = update.callback_query;
      chatId = callbackQuery.message.chat.id;
      messageId = callbackQuery.message.message_id;
      textData = callbackQuery.data; // callback data
      userId = callbackQuery.from.id;
      firstName = callbackQuery.from.first_name;
      username = callbackQuery.from.username;
      callbackQueryId = callbackQuery.id;

      logger.info(`Callback from ${userId} (${username || firstName}): ${textData} in chat ${chatId}, msgId: ${messageId}`);

      await telegramService.answerCallbackQuery(callbackQueryId); // Acknowledge callback

      const user = await userService.findUserById(userId);
      if (!user) {
        logger.error(`User not found for callback query. ID: ${userId}`);
        // Optionally send a message to the user or just log
        return;
      }

      const botState = await userService.getBotSettings();
      const buttonValues = await userService.getButtonValues();
      user.isAdmin = (String(userId) === process.env.TELEGRAM_ADMIN_CHAT_ID);


      if (textData === 'mainMenu') {
        const mainMenuText = `منوی اصلی:`; // Simplified from mainValues['start_message']
        await telegramService.editToMainMenu(chatId, messageId, mainMenuText, user, botState, buttonValues);
      }
      // ... other callbacks
    }
  } catch (error) {
    logger.error('Error in handleUpdate:', error);
    if (chatId) { // Ensure chatId is defined before trying to send a message
        // await telegramService.sendMessage(chatId, "متاسفانه مشکلی در پردازش درخواست شما پیش آمده است.");
    }
    // Optionally send an error message to the admin
    if (process.env.TELEGRAM_ADMIN_CHAT_ID) {
      // await telegramService.sendMessage(process.env.TELEGRAM_ADMIN_CHAT_ID, `Error processing update for user ${userId || 'N/A'}: ${error.message}\nUpdate: ${JSON.stringify(update)}`);
    }
  }
};
