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
 * Interface BankListInterface
 */
interface BankListInterface
{
    /**
     * @return array
     */
    public function getCachedList(): array;

    /**
     * @return array
     */
    public function getList(): array;
}
