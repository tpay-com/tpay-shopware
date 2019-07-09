<?php
namespace TpayShopwarePayments\Components\TpayPayment;

use tpayLibs\src\_class_tpay\Notifications\BasicNotificationHandler;

class TpayBasicNotificationHandler extends BasicNotificationHandler
{
    public function __construct($merchantId, $merchantSecret)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        parent::__construct();
    }

}
