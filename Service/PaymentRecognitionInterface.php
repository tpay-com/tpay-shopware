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

namespace TpayShopwarePayments\Service;

/**
 * Interface PaymentRecognitionInterface
 */
interface PaymentRecognitionInterface
{
    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isTpay(int $paymentID): bool;

    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isBlik(int $paymentID): bool;

    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isCard(int $paymentID): bool;

    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isBankTransfer(int $paymentID): bool;
}
