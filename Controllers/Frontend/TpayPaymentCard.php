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
use TpayShopwarePayments\TpayShopwarePayments as Plugin;

class Shopware_Controllers_Frontend_TpayPaymentCard extends TpayPaymentController
{
    /** @var TpayBasicApi */
    protected $tpayApi;

    public function preDispatch()
    {
        $this->tpayApi = $this->container->get('tpay_shopware_payments.basic_api');
        parent::preDispatch();
    }

    /**
     * Register transaction and redirect to tPay
     *
     * @throws Exception
     */
    public function indexAction()
    {
        $transactionConfig = $this->getTransactionConfig();
        $transactionConfig['group'] = Plugin::CARD;

        $tpayTransaction = $this->createTransaction($transactionConfig);

        if ($tpayTransaction['result'] !== 1) {
            $this->redirect($this->errorReturn());

            return;
        }

        $this->updateTransactionID($tpayTransaction['title']);
        $this->redirect($tpayTransaction['url']);
    }
}
