<?php
namespace TpayShopwarePayments\Components\TpayPayment;

use tpayLibs\src\_class_tpay\PaymentForms\PaymentBasicForms;

class TpayBasicForm extends PaymentBasicForms
{
    public function __construct($merchantId, $merchantSecret)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        parent::__construct();
    }

}
