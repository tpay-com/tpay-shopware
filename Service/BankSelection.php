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

use Enlight_Components_Session_Namespace as Session;
use Psr\Log\LoggerInterface;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;

/**
 * Saves and returns the selected payment channel.
 *
 * Class BankSelection
 */
class BankSelection implements BankSelectionInterface
{
    /** @var Session */
    protected $session;

    /** @var DataPersister */
    protected $dataPersister;

    /** @var DataLoader */
    protected $dataLoader;

    /** @var BankList */
    protected $bankList;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * BankSelection constructor.
     *
     * @param Session           $session
     * @param DataPersister     $dataPersister
     * @param DataLoader        $dataLoader
     * @param BankListInterface $bankList
     * @param LoggerInterface   $logger
     */
    public function __construct(Session $session, DataPersister $dataPersister, DataLoader $dataLoader, BankListInterface $bankList, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->dataPersister = $dataPersister;
        $this->dataLoader = $dataLoader;
        $this->bankList = $bankList;
        $this->logger = $logger;
    }

    /**
     * @param int    $id
     * @param string $name
     * @param string $img
     *
     * @return bool
     */
    public function saveUser(int $id, string $name, string $img): bool
    {
        $userID = (int) $this->session->get('sUserId');
        if (empty($userID)) {
            return false;
        }
        try {
            $this->dataPersister->persist([
                'tpay_bank_id' => $id,
                'tpay_bank_name' => $name,
                'tpay_bank_logo' => $img,
            ], 's_user_attributes', $userID);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function currentUserSelected(): array
    {
        $userID = (int) $this->session->get('sUserId');
        $selected = [
            'id' => null,
            'name' => null,
            'img' => null,
            'status' => false,
        ];
        if (empty($userID)) {
            return $selected;
        }
        try {
            $data = $this->dataLoader->load('s_user_attributes', $userID);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return  $selected;
        }
        $selected['id'] = (!empty($data['tpay_bank_id']) ? (int) $data['tpay_bank_id'] : null);
        $selected['name'] = (!empty($data['tpay_bank_name']) ? $data['tpay_bank_name'] : null);
        $selected['img'] = (!empty($data['tpay_bank_logo']) ? $data['tpay_bank_logo'] : null);
        $selected['status'] = $this->methodAvailable((int) $selected['id']);

        return $selected;
    }

    /**
     * Checks if the method is still available.
     *
     * @param int $methodID
     *
     * @return bool
     */
    public function methodAvailable(int $methodID): bool
    {
        if (empty($methodID)) {
            return false;
        }
        $list = $this->bankList->getCachedList();

        return (bool) array_key_exists($methodID, $list);
    }

    /**
     * We save a specific payment channel in the order.
     * For future use.
     *
     * @param int $orderID
     *
     * @return bool
     */
    public function saveInOrderDetails(int $orderID)
    {
        $data = $this->currentUserSelected();
        try {
            $this->dataPersister->persist([
                'tpay_bank_id' => $data['id'],
                'tpay_bank_name' => $data['name'],
                'tpay_bank_logo' => $data['img'],
            ], 's_order_attributes', $orderID);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }
    }
}
