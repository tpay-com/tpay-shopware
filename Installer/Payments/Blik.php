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

namespace TpayShopwarePayments\Installer\Payments;

/**
 * Class Blik
 */
class Blik extends Payment
{
    public function __construct()
    {
        $this->name = 'tpay_blik';
        $this->description = 'Blik';
        $this->action = 'TpayPaymentBlik';
        $this->active = 0;
        $this->position = 1;
        $this->additionalDescription = '<img class="tpay__payment__icon" src="https://secure.tpay.com/_/g/150.png"/>'
            . '<div class="tpay__payment__description">'
            . '  Blik'
            . '</div>';
    }
}
