<?php
namespace TpayShopwarePayments\Components\TpayPayment;

/**
 * Interface TpayConfigInterface
 * @package TpayPaymentPlugin\Components\TpayPayment
 */
interface TpayConfigInterface
{
    /**
     * Merchant ID
     *
     * @return int
     */
    public function getMerchantID();

    /**
     * Merchant secret
     *
     * @return string
     */
    public function getMerchantSecret();

    /**
     * Transaction API key
     *
     * @return string
     */
    public function getTransactionApiKey();

    /**
     * Transaction API key password
     *
     * @return string
     */
    public function getTransactionApiPassword();

}
