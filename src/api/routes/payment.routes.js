const express = require('express');
const router = express.Router();
// const paymentController = require('../controllers/payment.controller'); // To be created

// Route for initiating payment (similar to pay/index.php)
// Example: GET /pay/?nowpayment&hash_id=some_hash
router.get('/', (req, res) => {
    // paymentController.initiatePayment(req, res);
    const { nowpayment, zarinpal, nextpay, hash_id } = req.query;
    console.log('Payment initiation request:', req.query);
    if (!hash_id) {
        return res.status(400).send('Error: hash_id is required.');
    }
    if (nowpayment !== undefined) {
        // Handle NowPayments
        res.send(`Initiating NowPayments for hash_id: ${hash_id}`);
    } else if (zarinpal !== undefined) {
        // Handle Zarinpal
        res.send(`Initiating Zarinpal for hash_id: ${hash_id}`);
    } else if (nextpay !== undefined) {
        // Handle NextPay
        res.send(`Initiating NextPay for hash_id: ${hash_id}`);
    } else {
        res.status(400).send('Error: No payment gateway specified or gateway not supported.');
    }
});

// Route for payment callback (similar to pay/back.php)
// Example: GET /pay/back?zarinpal&Authority=xxx&Status=OK&hash_id=some_hash
// Example: POST /pay/back/nowpayment (NowPayments might use POST for IPN)
router.all('/back', (req, res) => { // Use .all to handle GET or POST
    // paymentController.handlePaymentCallback(req, res);
    console.log('Payment callback received:', req.method, req.query, req.body);
    const gateway = Object.keys(req.query).find(k => ['zarinpal', 'nextpay', 'nowpaymentipn'].includes(k)); // nowpaymentipn as example

    if (gateway) {
        res.send(`Callback received for ${gateway}. Data: ${JSON.stringify(req.query)} ${JSON.stringify(req.body)}`);
    } else if (req.body && req.body.payment_id && req.body.payment_status) { // Basic check for NowPayments IPN via POST
        res.send(`NowPayments IPN received for payment_id: ${req.body.payment_id}, status: ${req.body.payment_status}`);
    }
    else {
        res.status(400).send('Error: Payment callback data missing or gateway not recognized.');
    }
});


module.exports = router;
