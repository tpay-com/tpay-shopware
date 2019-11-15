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
 * Interface BankSelectionInterface
 */
interface BankSelectionInterface
{
    /**
     * @param int    $id
     * @param string $name
     * @param string $img
     *
     * @return bool
     */
    public function saveUser(int $id, string $name, string $img): bool;

    /**
     * @return array
     */
    public function currentUserSelected(): array;

    /**
     * @param int $methodID
     *
     * @return bool
     */
    public function methodAvailable(int $methodID): bool;

    /**
     * @param int $orderID
     *
     * @return bool
     */
    public function saveInOrderDetails(int $orderID);
}
