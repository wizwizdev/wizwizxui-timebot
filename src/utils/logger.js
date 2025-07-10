// A simple logger, can be replaced with a more robust one like Winston or Pino

const getTimestamp = () => new Date().toISOString();

const info = (message, ...args) => {
  console.log(`[INFO] ${getTimestamp()} - ${message}`, ...args);
};

const error = (message, ...args) => {
  console.error(`[ERROR] ${getTimestamp()} - ${message}`, ...args);
};

const warn = (message, ...args) => {
  console.warn(`[WARN] ${getTimestamp()} - ${message}`, ...args);
};

const debug = (message, ...args) => {
  // console.debug(`[DEBUG] ${getTimestamp()} - ${message}`, ...args); // Uncomment if needed
};

module.exports = {
  info,
  error,
  warn,
  debug,
};
