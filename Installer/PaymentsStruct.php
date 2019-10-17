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

namespace TpayShopwarePayments\Installer;

use TpayShopwarePayments\Installer\Payments\BankTransfer;
use TpayShopwarePayments\Installer\Payments\Blik;
use TpayShopwarePayments\Installer\Payments\Card;
use TpayShopwarePayments\Installer\Payments\Payment;

/**
 * Class PaymentsStruct
 */
class PaymentsStruct
{
    /** @var Blik */
    protected $blik;

    /** @var Card */
    protected $card;

    /** @var BankTransfer */
    protected $bankTransfer;

    public function __construct()
    {
        $this->card = new Card();
        $this->blik = new Blik();
        $this->bankTransfer = new BankTransfer();
    }

    /**
     * @return Blik
     */
    public function getBlik(): Blik
    {
        return $this->blik;
    }

    /**
     * @return Card
     */
    public function getCard(): Card
    {
        return $this->card;
    }

    /**
     * @return BankTransfer
     */
    public function getBankTransfer(): BankTransfer
    {
        return $this->bankTransfer;
    }

    /**
     * @param int  $pluginID
     * @param bool $asArray
     *
     * @return \Generator
     */
    public function getAll(int $pluginID = 0, bool $asArray = false)
    {
        $vars = get_object_vars($this);
        foreach ($vars as $property => $value) {
            if (!$value instanceof Payment) {
                continue;
            }
            $value->setPluginID($pluginID);
            if ($asArray) {
                yield $value->jsonSerialize();
            } else {
                yield $value;
            }
        }
    }
}
