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

use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;
use tpayLibs\src\_class_tpay\Utilities\TException;
use TpayShopwarePayments\Components\TpayPayment\TpayBasicNotificationHandler;
use TpayShopwarePayments\Components\TpayPayment\TpayConfigInterface;

/**
 * Class Shopware_Controllers_Frontend_TpayPaymentWebhook
 */
class Shopware_Controllers_Frontend_TpayPaymentWebhook extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /** @var TpayBasicNotificationHandler */
    protected $transactionNotification;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var ModelManager */
    private $modelManager;

    /** @var TpayConfigInterface */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'notify',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch()
    {
        $this->modelManager = $this->container->get('models');
        $this->transactionNotification = $this->container->get('tpay_shopware_payments.transaction_notification');
        $this->logger = $this->container->get('tpaylogger');
        $this->config = $this->container->get('tpay_shopware_payments.components.tpay_payment.config');
    }

    /**
     * Check Tpay notification and update order payment status
     *
     * @throws TException
     */
    public function notifyAction()
    {
        $this->transactionNotification->enableForwardedIPValidation()->disableValidationServerIP(); //TODO Only for DEBUG
        $notification = $this->transactionNotification->checkPayment();
        /** @var Order $orderRepository */
        $orderRepository = $this->modelManager
            ->getRepository(Order::class)
            ->findOneBy([
                'transactionId' => $notification['tr_id'],
                'temporaryId' => $notification['tr_crc'],
            ]);
        if ($orderRepository === null) {
            $this->logger->error(sprintf('Could not find associated order with the temporaryId %s',
                $notification['tr_crc']));
            throw new TException(
                sprintf('Could not find associated order with the temporaryId %s', $notification['tr_crc'])
            );
        }

        $orderTotal = $orderRepository->getInvoiceAmount();
        $statusId = $this->getPaymentStatusId($notification, $orderTotal);
        /** @var Status $orderStatusModel */
        $comment = isset($notification['test_mode']) && $notification['test_mode'] === 1 ? 'TEST MODE PAYMENT' : null;
        $order = Shopware()->Modules()->Order();
        $order->setPaymentStatus($orderRepository->getId(), $statusId, $this->config->getSendStatusChangeEmail(), $comment);
        try {
            $this->modelManager->flush($orderRepository);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Transaction notify error: %s', $e->getMessage()));
        }
        // Disable Shopware Smarty renderer.
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
    }

    /**
     * @param array $notification
     * @param float $orderTotal
     *
     * @return int
     */
    private function getPaymentStatusId($notification, $orderTotal)
    {
        if ($notification['tr_status'] === 'CHARGEBACK') {
            $status = Status::PAYMENT_STATE_RE_CREDITING;
        } elseif ($notification['tr_paid'] < $orderTotal || $notification['tr_error'] === 'surcharge') {
            $status = Status::PAYMENT_STATE_PARTIALLY_PAID;
        } elseif (
            ($notification['tr_error'] === 'none' || $notification['tr_error'] === 'overpay')
            && $notification['tr_status'] === 'TRUE'
        ) {
            $status = Status::PAYMENT_STATE_COMPLETELY_PAID;
        } else {
            $status = Status::PAYMENT_STATE_REVIEW_NECESSARY;
        }

        return $status;
    }
}
