<?php

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;
use TpayShopwarePayments\Components\TpayPayment\TpayBasicNotificationHandler;
use tpayLibs\src\_class_tpay\Utilities\TException;
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_TpayPaymentWebhook extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * @var TpayBasicNotificationHandler
     */
    protected $transactionNotification;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

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
    }

    /**
     * Check Tpay notification and update order payment status
     * @throws TException
     */
    public function notifyAction()
    {
        $notification = $this->transactionNotification->checkPayment();
        /** @var Order $orderRepository */
        $orderRepository = $this->modelManager
            ->getRepository(Order::class)
            ->findOneBy([
                'transactionID' => $notification['tr_id'],
                'temporaryID' => base64_decode($notification['tr_crc']),
            ]);
        if ($orderRepository === null) {
            $this->logger->error(sprintf('Could not find associated order with the temporaryID %s', $notification['tr_crc']));
            throw new TException(
                sprintf('Could not find associated order with the temporaryID %s', $notification['tr_crc'])
            );
        }
        $orderTotal = $orderRepository->getInvoiceAmount();
        $status = $this->getPaymentStatus($notification, $orderTotal);
        /** @var Status $orderStatusModel */
        $orderStatusModel = $this->modelManager
            ->getRepository(Status::class)
            ->find($status);
        $orderRepository->setPaymentStatus($orderStatusModel);
        $orderRepository->setTransactionId($notification['tr_id']);
        try{
            $this->modelManager->flush($orderRepository);
        }catch (\Exception $e){
            $this->logger->error(sprintf('Transaction notify error: %s', $e->getMessage()));
        }
        // Disable Shopware Smarty renderer.
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
    }

    /**
     * @param array $notification
     * @param float $orderTotal
     * @return int
     */
    private function getPaymentStatus($notification, $orderTotal)
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
