const bot = require('../../config/telegram');
const logger = require('../../utils/logger');
const db = require('../../config/db'); // For dynamic buttons

/**
 * Sends a message to a specific chat ID.
 */
async function sendMessage(chatId, text, options = {}) {
  try {
    await bot.sendMessage(chatId, text, options);
    logger.info(`Message sent to ${chatId}: "${text.substring(0, 50)}..."`);
  } catch (error) {
    logger.error(`Error sending message to ${chatId}: ${error.message} (Options: ${JSON.stringify(options)})`);
    // Do not throw here, allow application to continue if a message fails.
  }
}

/**
 * Edits a message text.
 */
async function editMessageText(chatId, messageId, text, options = {}) {
  try {
    await bot.editMessageText(text, { chat_id: chatId, message_id: messageId, ...options });
    logger.info(`Message ${messageId} in chat ${chatId} edited.`);
  } catch (error) {
    logger.error(`Error editing message ${messageId} in chat ${chatId}: ${error.message}`);
  }
}

/**
 * Edits a message reply markup.
 */
async function editMessageReplyMarkup(chatId, messageId, replyMarkup) {
  try {
    await bot.editMessageReplyMarkup(replyMarkup, { chat_id: chatId, message_id: messageId });
    logger.info(`Reply markup for message ${messageId} in chat ${chatId} edited.`);
  } catch (error) {
    logger.error(`Error editing reply markup for message ${messageId} in chat ${chatId}: ${error.message}`);
  }
}

/**
 * Answers a callback query.
 */
async function answerCallbackQuery(callbackQueryId, text, showAlert = false) {
  try {
    await bot.answerCallbackQuery(callbackQueryId, { text, show_alert: showAlert });
  } catch (error) {
    // Errors here are often due to answering too late or a bad query ID, usually not critical.
    logger.warn(`Error answering callback query ${callbackQueryId}: ${error.message}`);
  }
}

// Function to generate main menu keyboard based on PHP's getMainKeys
async function generateMainMenuKeyboard(userInfo, botState, buttonValues) {
  const keyboard = [];
  const isAdmin = (String(userInfo.userid) === process.env.TELEGRAM_ADMIN_CHAT_ID) || userInfo.isAdmin === 1 || userInfo.isAdmin === true;


  if (botState.agencyState === "on" && userInfo.is_agent === 1) {
    keyboard.push([{ text: buttonValues.agency_setting, callback_data: "agencySettings" }]);
    keyboard.push([
      { text: buttonValues.agent_one_buy, callback_data: "agentOneBuy" },
      { text: buttonValues.agent_much_buy, callback_data: "agentMuchBuy" }
    ]);
    keyboard.push([{ text: buttonValues.agent_configs_list || buttonValues.my_subscriptions, callback_data: "agentConfigsList" }]);
  } else {
    if (botState.agencyState === "on" && (userInfo.is_agent === 0 || userInfo.is_agent === null)) {
      keyboard.push([{ text: buttonValues.request_agency, callback_data: "requestAgency" }]);
    }
    if (botState.sellState === "on" || isAdmin) {
      keyboard.push([
        { text: buttonValues.my_subscriptions, callback_data: 'mySubscriptions' },
        { text: buttonValues.buy_subscriptions, callback_data: "buySubscription" }
      ]);
    } else {
      keyboard.push([{ text: buttonValues.my_subscriptions, callback_data: 'mySubscriptions' }]);
    }
  }

  if (botState.testAccount === "on") {
    keyboard.push([{ text: buttonValues.test_account, callback_data: "getTestAccount" }]);
  }
  keyboard.push([{ text: buttonValues.sharj, callback_data: "increaseMyWallet" }]);
  keyboard.push([
    { text: buttonValues.invite_friends, callback_data: "inviteFriends" },
    { text: buttonValues.my_info, callback_data: "myInfo" }
  ]);

  if (botState.sharedExistence === "on" && botState.individualExistence === "on") {
    keyboard.push([
        { text: buttonValues.shared_existence, callback_data: "availableServers" },
        { text: buttonValues.individual_existence, callback_data: "availableServers2" }
    ]);
  } else if (botState.sharedExistence === "on") {
    keyboard.push([{ text: buttonValues.shared_existence, callback_data: "availableServers" }]);
  } else if (botState.individualExistence === "on") {
    keyboard.push([{ text: buttonValues.individual_existence, callback_data: "availableServers2" }]);
  }

  keyboard.push([
      { text: buttonValues.application_links, callback_data: "reciveApplications" },
      { text: buttonValues.my_tickets, callback_data: "supportSection" }
  ]);

  if (botState.searchState === "on" || isAdmin) {
      keyboard.push([{ text: buttonValues.search_config, callback_data: "showUUIDLeft" }]);
  }

  // Fetch dynamic main buttons from `setting` table
  const connection = await db.getConnection();
  try {
    const [dynamicButtonRows] = await connection.execute("SELECT * FROM `setting` WHERE `type` LIKE '%MAIN_BUTTONS%'");
    let tempRow = [];
    for (const row of dynamicButtonRows) {
      const rowId = row.id; // Assuming 'id' is the PK for these button settings
      const title = row.type.replace("MAIN_BUTTONS", ""); // Extract title
      tempRow.push({ text: title, callback_data: `showMainButtonAns${rowId}` });
      if (tempRow.length >= 2) {
        keyboard.push([...tempRow]);
        tempRow = [];
      }
    }
    if (tempRow.length > 0) {
      keyboard.push([...tempRow]);
    }
  } catch (dbError) {
    logger.error("Error fetching dynamic main buttons:", dbError);
  } finally {
    if (connection) connection.release();
  }

  if (isAdmin) {
    keyboard.push([{ text: "مدیریت ربات ⚙️", callback_data: "managePanel" }]);
  }

  return { inline_keyboard: keyboard };
}


async function sendMainMenu(chatId, text, userInfo, botState, buttonValues) {
  const replyMarkup = await generateMainMenuKeyboard(userInfo, botState, buttonValues);
  await sendMessage(chatId, text, { reply_markup: replyMarkup });
}

async function editToMainMenu(chatId, messageId, text, userInfo, botState, buttonValues) {
  const replyMarkup = await generateMainMenuKeyboard(userInfo, botState, buttonValues);
  await editMessageText(chatId, messageId, text, { reply_markup: replyMarkup });
}


module.exports = {
  sendMessage,
  editMessageText,
  editMessageReplyMarkup,
  answerCallbackQuery,
  sendMainMenu,
  editToMainMenu,
  generateMainMenuKeyboard
};
