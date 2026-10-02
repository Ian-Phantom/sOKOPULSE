<?php
require_once __DIR__ . '/config.php';
header('Content-Type: text/plain; charset=utf-8');

echo "SokoPulse v2 Daraja configuration check\n\n";
echo 'Consumer Key: ' . (MPESA_CONSUMER_KEY !== '' ? 'configured' : 'MISSING') . "\n";
echo 'Consumer Secret: ' . (MPESA_CONSUMER_SECRET !== '' ? 'configured' : 'MISSING') . "\n";
echo 'STK Shortcode: ' . (MPESA_SHORTCODE !== '' ? 'configured' : 'MISSING') . "\n";
echo 'STK Passkey: ' . (MPESA_PASSKEY !== '' ? 'configured' : 'MISSING') . "\n";
echo 'Callback URL: ' . MPESA_CALLBACK_URL . "\n";
echo "\nThis page does not send money or initiate a transaction.\n";
