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
 * Class BankTransfer
 */
class BankTransfer extends Payment
{
    public function __construct()
    {
        $this->name = 'tpay_bank_transfer';
        $this->description = 'Szybki przelew';
        $this->action = 'TpayPayment';
        $this->active = 0;
        $this->position = 2;
        $this->template = 'tpay_bank_transfer.tpl';
        $this->additionalDescription = '<img class="tpay__payment__icon" src="https://tpay.com/img/banners/tpay-160x75.svg"/>'
            . '<div class="tpay__payment__description">'
            . '  Bank transfer'
            . '</div>';
    }
}
