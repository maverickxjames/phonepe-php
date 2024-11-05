<?php

// Autoload the classes using Composer
require 'vendor/autoload.php';

// Manually include the necessary SDK files if autoload isn't working
// Adjust these paths if needed
include 'vendor/phonepe/phonepe-pg-php-sdk/src/phonepe/sdk/pg/payments/v1/PhonePePaymentClient.php';
include 'vendor/phonepe/phonepe-pg-php-sdk/src/phonepe/sdk/pg/Env.php'; // Ensure this file exists

use PhonePe\Env;
use PhonePe\payments\v1\PhonePePaymentClient;
use PhonePe\payments\v1\models\request\builders\PgPayRequestBuilder;
use PhonePe\payments\v1\models\request\builders\InstrumentBuilder;

// Define merchant credentials and environment
const MERCHANTID = "<merchant_id>";
const SALTKEY = "<salt_key>";
const SALTINDEX = 1;

// Environment can be "PRODUCTION" or "UAT"
$env = Env::PRODUCTION;

// Enable event publishing to PhonePe
$shouldPublishEvents = true;

// Initialize PhonePe client
$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, $env, $shouldPublishEvents);

// Transaction details
$merchantTransactionId = uniqid('txn_');
$amountInPaise = 100;
$mobileNumber = "9999999999";
$merchantUserId = "user123";
$callbackUrl = "<callback_url>";
$redirectUrl = "<redirect_url>";

// Build the payment request
$request = PgPayRequestBuilder::builder()
    ->mobileNumber($mobileNumber)
    ->callbackUrl($callbackUrl)
    ->merchantId(MERCHANTID)
    ->merchantUserId($merchantUserId)
    ->amount($amountInPaise)
    ->merchantTransactionId($merchantTransactionId)
    ->redirectUrl($redirectUrl)
    ->redirectMode("REDIRECT")
    ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
    ->build();

// Execute the payment
$response = $phonePePaymentsClient->pay($request);

// Get the redirect URL
$payPageUrl = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();

// Redirect user to the payment page
header("Location: $payPageUrl");
exit;

