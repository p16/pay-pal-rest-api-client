Payment authorization and capture with paypal method
====================================================

[PayPal documentation](https://developer.paypal.com/docs/integration/direct/capture-payment/)

```php
<?php

use Guzzle\Http\Client;
use PayPalRestApiClient\Repository\AccessTokenRepository;
use PayPalRestApiClient\Service\PaymentService;
use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;
use PayPalRestApiClient\Builder\PaymentBuilder;
use PayPalRestApiClient\Model\Amount;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Payer;

$baseUrl = 'https://api.sandbox.paypal.com';
$returnUrl = 'http://example.com/success';
$cancelUrl = 'http://example.com/cancel';

$client = new Client();

$repo = new AccessTokenRepository(
    $client,
    $baseUrl
);
$accessToken = $repo->getAccessToken($clientId, $secret);

$paymentService = new PaymentService(
    $client,
    new PaymentRequestBodyBuilder(),
    $baseUrl
);

$itemList = array(
    'items' => array(
        array(
            'quantity' => 1,
            'name' => 'product name',
            'price' => '12.35',
            'currency' => 'EUR',
            'sku' => '1233456789',
        ),
    )
);

$amount = new Amount('EUR', '12.35');
$transaction = new Transaction($amount, 'my transaction', $itemList);

$paymentAuthorization = $paymentService->authorize(
    $accessToken,
    new Payer('paypal'),
    array(
        'return_url' => $returnUrl,
        'cancel_url' => $cancelUrl
    ),
    array($transaction)
);

$_SESSION['payment_data'] = $paymentAuthorization->getPaypalData();
// or
// $_SESSION['payment_data'] = serialize($paymentAuthorization);

$redirectUrl = $paymentAuthorization->getApprovalUrl();

/* redirects the user to $redirectUrl */
/* coming back from PayPal http://example.com/success?token=EC-9VK533621R3302713&PayerID=CBMFXGW3CHM7Q */

$payerId = $_GET['PayerID'];

$paymentService = new PaymentService(
    $client,
    new PaymentRequestBodyBuilder(),
    $baseUrl
);

$paymentBuilder = new PaymentBuilder();
$originalPayment = $paymentBuilder->build($_SESSION['payment_data']);
// or
// $originalPayment = unserialize($_SESSION['payment_data']);

$paymentAuthorization = $paymentService->execute($accessToken, $originalPayment, $payerId);

/* 'approved' === $paymentAuthorization->getState() */

/* Now you can capture the payment with: */

$paymentService = new PaymentService(
    $client,
    new PaymentRequestBodyBuilder(),
    $baseUrl
);

$capture = $paymentService->capture($accessToken, $paymentAuthorization);

/* 'completed' === $capture->getState() */
```
