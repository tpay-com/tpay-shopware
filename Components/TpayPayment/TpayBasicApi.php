<?php
/**
 * This file is part of the Tpay Shopware Plugin.
 *
 * @copyright 2019 Tpay Krajowy Integrator Płatności S.A.
 * @link https://tpay.com/
 * @support pt@tpay.com
 *
 * @author Mateusz Flasiński
 * @author Piotr Jóźwiak
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TpayShopwarePayments\Components\TpayPayment;

use tpayLibs\src\_class_tpay\PaymentBlik;

/**
 * Class TpayBasicApi
 */
class TpayBasicApi extends PaymentBlik
{
    /**
     * TpayBasicApi constructor.
     *
     * @param int    $merchantId
     * @param string $merchantSecret
     * @param string $apiKey
     * @param string $apiPass
     */
    public function __construct($merchantId, $merchantSecret, $apiKey, $apiPass)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->trApiKey = $apiKey;
        $this->trApiPass = $apiPass;
        parent::__construct();
    }
}
