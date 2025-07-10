const mysql = require('mysql2/promise'); // استفاده از نسخه promise برای async/await

const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  charset: 'utf8mb4'
});

// تست اتصال اولیه (اختیاری)
async function testConnection() {
  try {
    const connection = await pool.getConnection();
    console.log('Successfully connected to the database.');
    connection.release();
  } catch (error) {
    console.error('Error connecting to the database:', error);
    // در صورت نیاز، برنامه را در اینجا متوقف کنید اگر اتصال به دیتابیس حیاتی است
    // process.exit(1);
  }
}

// testConnection(); // فراخوانی برای تست در زمان راه‌اندازی برنامه

module.exports = pool;
