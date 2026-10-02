<?php
define('DB_HOST','127.0.0.1'); define('DB_NAME','sokopulse'); define('DB_USER','root'); define('DB_PASS','');
define('APP_NAME','SokoPulse'); define('APP_URL','http://localhost/SokoPulse_v2');
define('MPESA_ENV','sandbox');
define('MPESA_CONSUMER_KEY',''); define('MPESA_CONSUMER_SECRET',''); define('MPESA_SHORTCODE','174379'); define('MPESA_PASSKEY','');
define('MPESA_CALLBACK_URL','YOUR_PUBLIC_URL/SokoPulse_v2/payment_callback.php');
define('MPESA_OAUTH_URL','https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
define('MPESA_STK_URL','https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
define('MPESA_STK_TYPE','CustomerPayBillOnline');
define('MPESA_TRANSACTION_DESCRIPTION','SokoPulse Order');
