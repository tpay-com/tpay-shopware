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
 * Class Card
 */
class Card extends Payment
{
    public function __construct()
    {
        $this->name = 'tpay_card';
        $this->description = 'Karta płatnicza';
        $this->action = 'TpayPaymentCard';
        $this->active = 0;
        $this->position = 3;
        $this->additionalDescription = '<img class="tpay__payment__icon" src="' . $this->getIconPath('card') . '"/>'
            . '<div class="tpay__payment__description">'
            . '  Karta płatnicza / kredytowa'
            . '</div>';
    }
}
