<?php
namespace TpayShopwarePayments\Components\TpayPayment;

use tpayLibs\src\_class_tpay\PaymentBlik;

class TpayBasicApi extends PaymentBlik
{
    public function __construct($merchantId, $merchantSecret, $apiKey, $apiPass)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->trApiKey = $apiKey;
        $this->trApiPass = $apiPass;
        parent::__construct();
    }

}
