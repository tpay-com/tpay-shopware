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

use Shopware\Models\Order\Status;
use tpayLibs\src\_class_tpay\Utilities\TException;
use TpayShopwarePayments\Components\TpayPayment\TpayBasicApi;
use TpayShopwarePayments\Controllers\Frontend\TpayPaymentController;
use TpayShopwarePayments\TpayShopwarePayments as Plugin;

/**
 * Class Shopware_Controllers_Frontend_TpayPaymentBlik
 */
class Shopware_Controllers_Frontend_TpayPaymentBlik extends TpayPaymentController
{
    /** @var TpayBasicApi */
    protected $tpayApi;

    public function preDispatch()
    {
        $this->tpayApi = $this->container->get('tpay_shopware_payments.basic_api');
        parent::preDispatch();
    }

    /**
     * Validate code and insert order
     */
    public function ajaxAction()
    {
        $blikCode = $this->request->get('code');

        $crc = $this->createPaymentUniqueId();

        $transactionConfig = $this->getTransactionConfig($crc, false);

        $checkCode = $this->createBlikTransaction($transactionConfig, $blikCode);
        if (!$checkCode) {
            $this->responseJSON(['success' => false]);

            return;
        }

        $this->insertOrder($this->transactionID, $crc);

        $this->responseJSON(['success' => true, 'number' => $this->getOrderNumber()]);
    }

    /**
     * Validate Order Status
     */
    public function checkAction()
    {
        $number = $this->request->get('number');

        $db = $this->container->get('dbal_connection');
        $qb = $db->createQueryBuilder();
        $query = $qb->select('cleared, temporaryID')
            ->from('s_order')
            ->where('ordernumber = :number')
            ->setParameter('number', $number)
            ->execute()
            ->fetch();

        if ((int) $query['cleared'] === Status::PAYMENT_STATE_COMPLETELY_PAID) {
            $url = $this->buildURL(['controller' => 'checkout', 'action' => 'finish', 'sUniqueID' => $query['temporaryID']]);
            $data = [
                'waiting' => false,
                'success' => true,
                'redirect' => $url,
            ];
        } elseif ((int) $query['cleared'] === Status::PAYMENT_STATE_OPEN) {
            $data = [
                'waiting' => true,
            ];
        } else {
            $data = [
                'waiting' => false,
                'success' => false,
                'redirect' => $this->errorReturn(),
            ];
        }

        $this->responseJSON($data);
    }

    /**
     * @param array  $transactionConfig
     * @param string $blikCode
     *
     * @return bool
     */
    private function createBlikTransaction(array $transactionConfig, string $blikCode): bool
    {
        $transactionConfig['group'] = Plugin::BLIK;
        try {
            $tpayTransaction = $this->tpayApi->create($transactionConfig);
            $this->transactionID = $tpayTransaction['title'];
        } catch (TException $TException) {
            $this->logger->error($TException->getMessage());

            return false;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }

        try {
            $responseBlik = $this->tpayApi->blik($tpayTransaction['title'], $blikCode);
            if (isset($responseBlik['result']) && (int) $responseBlik['result'] === 1) {
                return true;
            }
        } catch (TException $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }
}
