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

/**
 * Interface TpayConfigInterface
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

    /**
     * Send order status change email setting
     *
     * @return bool
     */
    public function getSendStatusChangeEmail();

    /**
     * 0 - Download all available payment channel groups
     * 1 - Download groups of payment channels available only online - for payment channels from these groups, booking is done within a short time
     *
     * @return int
     */
    public function getChannelsType();
}
