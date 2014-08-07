<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;

class PaymentRequestBodyBuilder
{
    protected $intent;
    protected $payer;
    protected $urls;
    protected $transactions;

    public function __construct(
        PayerBuilder $payerBuilder,
        UrlsBuilder $urlsBuilder,
        TransactionsBuilder $transactionsBuilder
    ) {
        $this->payerBuilder = $payerBuilder;
        $this->urlsBuilder = $urlsBuilder;
        $this->transactionsBuilder = $transactionsBuilder;
    }

    public function build($intent, $payer, $urls, $transactions)
    {
        $this->assertIntent($intent);
        $this->intent = $intent;

        $requestBody = array();
        $requestBody['intent'] = $this->intent;
        $requestBody['payer'] = $this->payerBuilder->buildArray($payer);
        $requestBody['redirect_urls'] = $this->urlsBuilder->buildArray($urls);
        $requestBody['transactions'] = $this->transactionsBuilder->buildArray($transactions);

        return $requestBody;
    }

    private function assertIntent($intent)
    {
        if ( ! in_array($intent, array('sale', 'authorize', 'order'))) {
            
            throw new BuilderException("intent is not valid: allowed value are 'sale', 'authorize', 'order'");
        }
    }

}
