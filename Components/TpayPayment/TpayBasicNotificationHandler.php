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

use tpayLibs\src\_class_tpay\Notifications\BasicNotificationHandler;

/**
 * Class TpayBasicNotificationHandler
 */
class TpayBasicNotificationHandler extends BasicNotificationHandler
{
    /**
     * TpayBasicNotificationHandler constructor.
     *
     * @param int    $merchantId
     * @param string $merchantSecret
     */
    public function __construct($merchantId, $merchantSecret)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        parent::__construct();
    }
}
