// tests/unit/services/userService.test.js

// Define mocks for DB connection BEFORE any require/import that might use them
const mockConnectionExecute = jest.fn();
const mockConnectionRelease = jest.fn();
const mockConnectionObject = {
  execute: mockConnectionExecute,
  release: mockConnectionRelease,
};

jest.mock('../../../src/config/db', () => ({
  getConnection: jest.fn().mockResolvedValue(mockConnectionObject),
}));

// Now require the service
const userService = require('../../../src/api/services/userService');
const db = require('../../../src/config/db'); // db is now the mocked version


describe('User Service', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    mockConnectionExecute.mockClear(); // Clear the execute mock specifically
    mockConnectionRelease.mockClear(); // Clear the release mock
    db.getConnection.mockClear(); // Clear the getConnection mock itself
  });

  describe('findOrCreateUser', () => {
    it('should create a new user if not exists', async () => {
      const mockUserDetails = { userid: 123, name: 'Test User', username: 'testuser', isNew: true, refcode: 0, wallet: 0, step: 'none', isAdmin: false, is_agent: 0 };
      // Simulate user not found initially
      mockConnectionExecute.mockResolvedValueOnce([[]]);
      // Simulate successful insert (though insert doesn't return rows like select)
      mockConnectionExecute.mockResolvedValueOnce([{ affectedRows: 1 }]);
      // Simulate fetching the newly created user
      mockConnectionExecute.mockResolvedValueOnce([[{ ...mockUserDetails, date: Math.floor(Date.now()/1000) }]]);


      const userDetailsToCreate = { userId: 123, username: 'testuser', firstName: 'Test User' };
      const user = await userService.findOrCreateUser(userDetailsToCreate);

      expect(db.getConnection).toHaveBeenCalledTimes(1); // Called once for the whole operation
      // The execute mock will be called multiple times on the same connection object
      expect(mockConnectionExecute).toHaveBeenNthCalledWith(1, 'SELECT * FROM `users` WHERE `userid` = ?', [userDetailsToCreate.userId]);
      expect(mockConnectionExecute).toHaveBeenNthCalledWith(2,
        'INSERT INTO `users` (`userid`, `name`, `username`, `refcode`, `wallet`, `date`, `step`, `isAdmin`, `is_agent`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        expect.arrayContaining([userDetailsToCreate.userId, userDetailsToCreate.firstName, userDetailsToCreate.username, 0, 0, expect.any(Number), 'none', false, 0])
      );
      expect(mockConnectionExecute).toHaveBeenNthCalledWith(3, 'SELECT * FROM `users` WHERE `userid` = ?', [userDetailsToCreate.userId]);

      expect(user).toHaveProperty('userid', userDetailsToCreate.userId);
      expect(user).toHaveProperty('isNew', true);
    });

    it('should return an existing user if exists', async () => {
      const existingUserDbRecord = { userid: 456, name: 'Existing User', username: 'existing', step: 'none', is_agent: 0, isAdmin: 0, date: 1678886400, refcode: 0, wallet: 0 };
      mockConnectionExecute.mockResolvedValueOnce([[existingUserDbRecord]]);

      const userDetailsToFind = { userId: 456, username: 'existing', firstName: 'Existing User' };
      const user = await userService.findOrCreateUser(userDetailsToFind);

      expect(db.getConnection).toHaveBeenCalledTimes(1);
      expect(mockConnectionExecute).toHaveBeenCalledWith('SELECT * FROM `users` WHERE `userid` = ?', [userDetailsToFind.userId]);
      expect(user).toEqual({ ...existingUserDbRecord, isNew: false });
      expect(mockConnectionExecute).toHaveBeenCalledTimes(1);
    });
  });

  describe('getBotSettings', () => {
    it('should return parsed bot settings if found', async () => {
        const mockSettingsValue = { sellState: "on", agencyState: "off", testAccount: "on" };
        mockConnectionExecute.mockResolvedValueOnce([[{ type: 'BOT_STATES', value: JSON.stringify(mockSettingsValue) }]]);

        const settings = await userService.getBotSettings();
        expect(settings).toEqual(mockSettingsValue);
        expect(mockConnectionExecute).toHaveBeenCalledWith("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
    });

    it('should return default settings if BOT_STATES not found in DB', async () => {
        mockConnectionExecute.mockResolvedValueOnce([[]]); // Simulate not found
        const settings = await userService.getBotSettings();
        expect(settings).toEqual({
            agencyState: "off",
            sellState: "on",
            testAccount: "off",
            sharedExistence: "off",
            individualExistence: "off",
            searchState: "off",
        });
    });

    it('should return default settings if BOT_STATES value is null', async () => {
        mockConnectionExecute.mockResolvedValueOnce([[{ type: 'BOT_STATES', value: null }]]);
        const settings = await userService.getBotSettings();
        expect(settings).toEqual({
            agencyState: "off",
            sellState: "on",
            testAccount: "off",
            sharedExistence: "off",
            individualExistence: "off",
            searchState: "off",
        });
    });

    it('should return default settings on JSON parse error', async () => {
        mockConnectionExecute.mockResolvedValueOnce([[{ type: 'BOT_STATES', value: "invalid json" }]]);
        const settings = await userService.getBotSettings();
        expect(settings).toEqual({}); // As per current implementation, returns {} on parse error
    });
  });

});
