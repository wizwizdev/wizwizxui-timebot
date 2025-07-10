const express = require('express');
const router = express.Router();
// const telegramController = require('../controllers/telegram.controller'); // بعداً اضافه می‌شود

// Webhook endpoint for Telegram
router.post('/webhook', (req, res) => {
    console.log("Received Telegram update:", JSON.stringify(req.body, null, 2));
    // telegramController.handleUpdate(req.body); // پردازش آپدیت
    res.sendStatus(200); // پاسخ سریع به تلگرام
});

module.exports = router;
