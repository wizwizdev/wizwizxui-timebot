// tests/unit/services/telegramService.test.js

// Define mocks for DB connection BEFORE any require/import that might use them
const mockTgServiceDbExecute = jest.fn();
const mockTgServiceDbRelease = jest.fn();
const mockTgServiceDbConnectionObject = {
  execute: mockTgServiceDbExecute,
  release: mockTgServiceDbRelease,
};

jest.mock('../../../src/config/db', () => ({
  getConnection: jest.fn().mockResolvedValue(mockTgServiceDbConnectionObject),
}));

// Mock کردن ماژول config/telegram
jest.mock('../../../src/config/telegram', () => ({
  sendMessage: jest.fn().mockResolvedValue({}),
  editMessageText: jest.fn().mockResolvedValue({}),
  editMessageReplyMarkup: jest.fn().mockResolvedValue({}),
  answerCallbackQuery: jest.fn().mockResolvedValue({})
}));

// Now require the service
const telegramService = require('../../../src/api/services/telegramService');
const botMock = require('../../../src/config/telegram'); // botMock is now the mocked version
const db = require('../../../src/config/db'); // db is now the mocked version for this test file


describe('Telegram Service', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    mockTgServiceDbExecute.mockClear();
    mockTgServiceDbRelease.mockClear();
    db.getConnection.mockClear();
  });

  describe('sendMessage', () => {
    it('should call bot.sendMessage with correct parameters', async () => {
      const chatId = 123;
      const text = 'Hello Test';
      const options = { parse_mode: 'Markdown' };
      await telegramService.sendMessage(chatId, text, options);
      expect(botMock.sendMessage).toHaveBeenCalledWith(chatId, text, options);
    });
  });

  describe('editMessageText', () => {
    it('should call bot.editMessageText with correct parameters', async () => {
      const chatId = 123;
      const messageId = 456;
      const text = 'New Text';
      const options = { parse_mode: 'HTML' };
      await telegramService.editMessageText(chatId, messageId, text, options);
      expect(botMock.editMessageText).toHaveBeenCalledWith(text, { chat_id: chatId, message_id: messageId, ...options });
    });
  });

  describe('editMessageReplyMarkup', () => {
    it('should call bot.editMessageReplyMarkup with correct parameters', async () => {
      const chatId = 123;
      const messageId = 789;
      const replyMarkup = { inline_keyboard: [[{ text: 'Test', callback_data: 'test' }]] };
      await telegramService.editMessageReplyMarkup(chatId, messageId, replyMarkup);
      expect(botMock.editMessageReplyMarkup).toHaveBeenCalledWith(replyMarkup, { chat_id: chatId, message_id: messageId });
    });
  });

  describe('answerCallbackQuery', () => {
    it('should call bot.answerCallbackQuery with correct parameters', async () => {
      const callbackQueryId = 'cb_query_id';
      const text = 'Alert!';
      const showAlert = true;
      await telegramService.answerCallbackQuery(callbackQueryId, text, showAlert);
      expect(botMock.answerCallbackQuery).toHaveBeenCalledWith(callbackQueryId, { text, show_alert: showAlert });
    });
  });

  describe('generateMainMenuKeyboard', () => {
    const mockButtonValues = {
        agency_setting: "Agency Settings", agent_one_buy: "Agent Buy One", agent_much_buy: "Agent Buy Many",
        agent_configs_list: "Agent Configs", request_agency: "Request Agency", my_subscriptions: "My Subscriptions",
        buy_subscriptions: "Buy Subscription", test_account: "Test Account", sharj: "Charge Wallet",
        invite_friends: "Invite Friends", my_info: "My Info", shared_existence: "Shared Servers",
        individual_existence: "Individual Servers", application_links: "App Links", my_tickets: "My Tickets",
        search_config: "Search Config", managePanel: "Admin Panel"
    };

    it('should generate admin menu for admin user', async () => {
        const adminUser = { userid: process.env.TELEGRAM_ADMIN_CHAT_ID || '123456789', is_agent: 0, isAdmin: true };
        const botState = { agencyState: "on", sellState: "on", testAccount: "on", sharedExistence: "on", individualExistence: "on", searchState: "on" };

        mockTgServiceDbExecute.mockResolvedValueOnce([[]]); // No dynamic buttons for this test case

        const keyboard = await telegramService.generateMainMenuKeyboard(adminUser, botState, mockButtonValues);
        const adminButtonExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.callback_data === 'managePanel')
        );
        expect(adminButtonExists).toBe(true);
        // Check a common button
        const buyButtonExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.callback_data === 'buySubscription')
        );
        expect(buyButtonExists).toBe(true);
    });

    it('should generate agent menu for an agent', async () => {
        const agentUser = { userid: 'agent1', is_agent: 1, isAdmin: false };
        const botState = { agencyState: "on", sellState: "on", testAccount: "off" };
        mockTgServiceDbExecute.mockResolvedValueOnce([[]]); // No dynamic buttons

        const keyboard = await telegramService.generateMainMenuKeyboard(agentUser, botState, mockButtonValues);
        const agentButtonExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.callback_data === 'agencySettings')
        );
        expect(agentButtonExists).toBe(true);
    });

    it('should generate basic menu for a normal user', async () => {
        const normalUser = { userid: 'user1', is_agent: 0, isAdmin: false };
        const botState = { agencyState: "off", sellState: "on", testAccount: "on" };
        mockTgServiceDbExecute.mockResolvedValueOnce([[]]); // No dynamic buttons

        const keyboard = await telegramService.generateMainMenuKeyboard(normalUser, botState, mockButtonValues);
        const buyButtonExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.callback_data === 'buySubscription')
        );
        expect(buyButtonExists).toBe(true);
        const testAccountButtonExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.callback_data === 'getTestAccount')
        );
        expect(testAccountButtonExists).toBe(true);
        const adminButtonExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.callback_data === 'managePanel')
        );
        expect(adminButtonExists).toBe(false);
    });

    it('should include dynamic buttons from database', async () => {
        const user = { userid: 'user2', is_agent: 0, isAdmin: false };
        const botState = { sellState: "on" };
        const dynamicButtons = [
            { id: 1, type: 'MAIN_BUTTONSButtonA', value: 'Answer A' },
            { id: 2, type: 'MAIN_BUTTONSButtonB', value: 'Answer B' }
        ];
        mockTgServiceDbExecute.mockResolvedValueOnce([dynamicButtons]);

        const keyboard = await telegramService.generateMainMenuKeyboard(user, botState, mockButtonValues);
        const dynamicButtonAExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.text === 'ButtonA' && button.callback_data === 'showMainButtonAns1')
        );
        const dynamicButtonBExists = keyboard.inline_keyboard.some(row =>
            row.some(button => button.text === 'ButtonB' && button.callback_data === 'showMainButtonAns2')
        );
        expect(dynamicButtonAExists).toBe(true);
        expect(dynamicButtonBExists).toBe(true);
    });
  });
});
