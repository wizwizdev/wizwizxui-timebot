// tests/integration/telegramBot.test.js

// Mock 'node-telegram-bot-api' to prevent actual bot initialization and network calls
jest.mock('node-telegram-bot-api', () => {
  // This is the constructor mock
  return jest.fn().mockImplementation(() => {
    // This is the instance mock
    return {
      on: jest.fn(),
      sendMessage: jest.fn().mockResolvedValue({ message_id: 'mock_message_id_instance' }), // Ensure sendMessage returns something expected if chained
      editMessageText: jest.fn().mockResolvedValue(true),
      answerCallbackQuery: jest.fn().mockResolvedValue(true),
      setWebHook: jest.fn().mockResolvedValue(true),
      deleteWebHook: jest.fn().mockResolvedValue(true),
      // Add any other methods that might be called on the bot instance
      // by your actual src/config/telegram.js or services using the bot
    };
  });
});

// Mock کردن سرویس تلگرام برای کنترل دقیق‌تر توابع سطح بالا (اختیاری اما می‌تواند مفید باشد)
// اگر telegramService مستقیماً توابع bot را از config/telegram import و استفاده می‌کند،
// و config/telegram به درستی mock شده، شاید این mock دیگر لازم نباشد یا باید متفاوت باشد.
// فعلاً آن را نگه می‌داریم تا ببینیم آیا با mock کردن node-telegram-bot-api مشکل حل می‌شود یا خیر.
jest.mock('../../src/api/services/telegramService', () => ({
    sendMessage: jest.fn().mockResolvedValue({}),
    sendMainMenu: jest.fn().mockResolvedValue({}),
    editToMainMenu: jest.fn().mockResolvedValue({}),
    answerCallbackQuery: jest.fn().mockResolvedValue({})
}));


// Define mocks for DB connection
const mockIntDbExecute = jest.fn();
const mockIntDbRelease = jest.fn();
const mockIntDbConnection = {
  execute: mockIntDbExecute,
  release: mockIntDbRelease,
};
jest.mock('../../src/config/db', () => ({
  getConnection: jest.fn().mockResolvedValue(mockIntDbConnection),
}));

// حالا که mock ها در بالا تنظیم شده‌اند، ماژول‌های اصلی را require می‌کنیم
const telegramController = require('../../src/api/controllers/telegram.controller');
const userService = require('../../src/api/services/userService');
const bot = require('../../src/config/telegram'); // Import the mocked bot instance for verification if needed, though direct interaction is via service


describe('Telegram Bot Integration Test (Controller Level)', () => {
  process.env.TELEGRAM_ADMIN_CHAT_ID = "123456789";

  beforeEach(() => {
    jest.clearAllMocks();
    // Reset db mocks for integration tests
    mockIntDbExecute.mockClear();
    mockIntDbRelease.mockClear();
    // db.getConnection is already a jest.fn(), so it's cleared by jest.clearAllMocks()
    // but explicitly clearing it again doesn't hurt.
    if (db && db.getConnection && db.getConnection.mockClear) {
        db.getConnection.mockClear();
    }


    // Default mock for findOrCreateUser and findUserById
    jest.spyOn(userService, 'findOrCreateUser').mockImplementation(async ({ userId, username, firstName }) => {
        return {
            userid: userId,
            name: firstName,
            username: username,
            isNew: false,
            is_agent: 0,
            isAdmin: (String(userId) === process.env.TELEGRAM_ADMIN_CHAT_ID),
            step: 'none'
            // Add other necessary fields with default values
        };
    });
    jest.spyOn(userService, 'findUserById').mockImplementation(async (userId) => {
        return {
            userid: userId,
            name: 'Mocked User',
            username: 'mockeduser',
            is_agent: 0,
            isAdmin: (String(userId) === process.env.TELEGRAM_ADMIN_CHAT_ID),
            step: 'none'
            // Add other necessary fields
        };
    });

    // Default mock for getBotSettings
    jest.spyOn(userService, 'getBotSettings').mockResolvedValue({
        agencyState: "on", sellState: "on", testAccount: "on",
        sharedExistence: "on", individualExistence: "on", searchState: "on",
        // ... other necessary botState fields
    });
    // Default mock for getButtonValues
    jest.spyOn(userService, 'getButtonValues').mockResolvedValue({
        my_subscriptions: '📱 کانفیگ های من', buy_subscriptions: '🛒 خرید کانفیگ جدید',
        test_account: '🎁 دریافت اکانت تست ', sharj: '✅ 💳 شارژ کیف پول',
        invite_friends: '🏆 زیر مجموعه گیری', my_info: '🧑‍💼 حساب کاربری',
        shared_existence: "❕ موجودی اشتراکی ", individual_existence: "❗️ موجودی اختصاصی ",
        application_links: '🧩 آموزش اتصال', my_tickets: "📨 تیکت های من",
        search_config: "🪫 مشخصات کانفیگ", managePanel: "مدیریت ربات ⚙️",
        request_agency: "🧑‍💼 درخواست نمایندگی"
        // ... add other necessary button values
    });
    // Default mock for dynamic main buttons in DB
    mockDbConnection.execute.mockImplementation((query) => {
        if (query.includes("FROM `setting` WHERE `type` LIKE '%MAIN_BUTTONS%'")) {
            return Promise.resolve([[]]); // No dynamic buttons by default
        }
        return Promise.resolve([[]]); // Default for other queries
    });
  });

  test('should respond to /start command and call sendMainMenu', async () => {
    const mockUpdate = {
      message: {
        message_id: 1,
        from: { id: 123, is_bot: false, first_name: 'Test', username: 'testuser' },
        chat: { id: 123, first_name: 'Test', username: 'testuser', type: 'private' },
        date: Math.floor(Date.now() / 1000),
        text: '/start',
      },
    };

    // Mock specific DB calls for this test if findOrCreateUser needs them
    // For findOrCreateUser:
    mockDbConnection.execute
        .mockResolvedValueOnce([[]]) // First SELECT for findOrCreateUser (user not found)
        .mockResolvedValueOnce([{ affectedRows: 1 }]) // INSERT for findOrCreateUser
        .mockResolvedValueOnce([[{ userid: 123, name: 'Test', username: 'testuser', is_agent: 0, isAdmin: false, step: 'none'}]]); // Second SELECT for findOrCreateUser


    await telegramController.handleUpdate(mockUpdate);

    expect(userService.findOrCreateUser).toHaveBeenCalledWith({
      userId: 123,
      username: 'testuser',
      firstName: 'Test',
    });
    expect(telegramService.sendMainMenu).toHaveBeenCalled();
    const sendMainMenuArgs = telegramService.sendMainMenu.mock.calls[0];
    expect(sendMainMenuArgs[0]).toBe(123); // chatId
    expect(sendMainMenuArgs[1]).toContain('سلام Test');
  });

  test('should handle mainMenu callback_query and call editToMainMenu', async () => {
    const mockUpdate = {
      callback_query: {
        id: 'query_id_mainmenu',
        from: { id: 456, is_bot: false, first_name: 'CallbackUser', username: 'cbuser' },
        message: {
          message_id: 10,
          chat: { id: 456, type: 'private' },
          date: Math.floor(Date.now() / 1000),
          text: 'Some previous message with buttons'
        },
        chat_instance: 'chat_inst_id_xyz',
        data: 'mainMenu',
      },
    };

    // Mock DB call for findUserById
    mockDbConnection.execute.mockResolvedValueOnce([[{ userid: 456, name: 'CallbackUser', username: 'cbuser', is_agent: 0, isAdmin: false, step: 'none' }]]);


    await telegramController.handleUpdate(mockUpdate);

    expect(telegramService.answerCallbackQuery).toHaveBeenCalledWith('query_id_mainmenu');
    expect(userService.findUserById).toHaveBeenCalledWith(456);
    expect(telegramService.editToMainMenu).toHaveBeenCalled();
    const editArgs = telegramService.editToMainMenu.mock.calls[0];
    expect(editArgs[0]).toBe(456); // chatId
    expect(editArgs[1]).toBe(10);  // messageId
    expect(editArgs[2]).toBe('منوی اصلی:');
  });

});
