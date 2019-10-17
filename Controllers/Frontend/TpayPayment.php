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

use TpayShopwarePayments\Components\TpayPayment\TpayBasicApi;
use TpayShopwarePayments\Controllers\Frontend\TpayPaymentController;
use TpayShopwarePayments\Service\BankSelection;

/**
 * Class Shopware_Controllers_Frontend_TpayPayment
 */
class Shopware_Controllers_Frontend_TpayPayment extends TpayPaymentController
{
    /** @var TpayBasicApi */
    protected $tpayApi;

    /** @var BankSelection */
    protected $bankSelection;

    public function preDispatch()
    {
        $this->tpayApi = $this->container->get('tpay_shopware_payments.basic_api');
        $this->bankSelection = $this->container->get('tpay_shopware_payments.service.bank_selection');
        parent::preDispatch();
    }

    /**
     * Create transaction and redirect to tPay
     *
     * @throws Exception
     */
    public function indexAction()
    {
        $bank = $this->getBank();
        $transactionConfig = $this->getTransactionConfig();
        if ($this->bankSelection->methodAvailable($bank)) {
            $transactionConfig['group'] = $bank;
        }

        $this->logger->info('Selected Bank:' . $bank);

        $transactionConfig['accept_tos'] = 1;
        $tpayTransaction = $this->createTransaction($transactionConfig);

        if ($tpayTransaction['result'] !== 1) {
            $this->redirect($this->errorReturn());

            return;
        }

        $this->saveOrderDetails();

        $this->updateTransactionID($tpayTransaction['title']);

        $this->redirect($tpayTransaction['url']);
    }

    /**
     * User selected Bank
     *
     * @return int
     */
    private function getBank(): int
    {
        $data = $this->bankSelection->currentUserSelected();

        return (int) $data['id'];
    }

    /**
     * Save Bank Details in Order
     */
    private function saveOrderDetails()
    {
        $id = (int) $this->container->get('dbal_connection')
            ->createQueryBuilder()
            ->select('id')
            ->from('s_order')
            ->where('ordernumber = :number')
            ->setParameter('number', $this->getOrderNumber())
            ->execute()
            ->fetchColumn();
        if (empty($id)) {
            return;
        }

        $this->bankSelection->saveInOrderDetails($id);
    }
}
