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

namespace TpayShopwarePayments\Controllers\Frontend;

use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Models\Order\Status;

abstract class TpayPaymentController extends \Shopware_Controllers_Frontend_Payment
{
    /** @var int */
    protected $currentOrderId;

    /** @var string */
    protected $transactionID;

    /** @var LoggerInterface */
    protected $logger;

    public function preDispatch()
    {
        $this->logger = $this->container->get('tpaylogger');
        parent::preDispatch();
    }

    /**
     * @param string $crc
     * @param bool   $insertOrder
     *
     * @return array
     */
    protected function getTransactionConfig(string $crc = '', bool $insertOrder = true): array
    {
        $user = $this->getUser();
        $billing = $user['billingaddress'];
        if (empty($crc)) {
            $crc = $this->createPaymentUniqueId();
        }
        if ($insertOrder) {
            $this->insertOrder($crc, $crc);
        }
        $parameter = [
            'amount' => $this->getAmount(),
            'crc' => $crc,
            'name' => $billing['firstname'] . ' ' . $billing['lastname'],
            'city' => $billing['city'],
            'address' => $billing['street'],
            'zip' => $billing['zipcode'],
            'return_url' => $this->getReturnUrl(),
            'return_error_url' => $this->getReturnUrl(),
            'result_url' => $this->getNotificationUrl(),
            'email' => $user['additional']['user']['email'],
            'language' => 'PL',
            'module' => 'Shopware',
        ];

        if (!empty($this->currentOrderId)) {
            $parameter['description'] = 'Zamówienie nr ' . $this->currentOrderId;
        } else {
            $parameter['description'] = 'Zamówienie w sklepie internetowym';
        }

        if (!empty($billing['phone'])) {
            $parameter['phone'] = $billing['phone'];
        }
        if (!empty($user['additional']['country']['countryiso'])) {
            $parameter['country'] = $user['additional']['country']['countryiso'];
            $parameter['language'] = $user['additional']['country']['countryiso'];
        }
        if (!in_array($parameter['language'], ['PL', 'EN', 'DE'])) {
            $parameter['language'] = 'PL';
        }

        return $parameter;
    }

    /**
     * @param string $tpayTransactionId Transaction title in Tpay system
     * @param string $paymentUniqueId   Additional random Id passed to Tpay
     */
    protected function insertOrder($tpayTransactionId, $paymentUniqueId)
    {
        $this->currentOrderId = $this->saveOrder(
            $tpayTransactionId,
            $paymentUniqueId,
            Status::PAYMENT_STATE_OPEN
        );
    }

    /**
     * @param string $transactionID
     */
    protected function updateTransactionID($transactionID)
    {
        $connection = $this->container->get('dbal_connection');

        $qb = $connection->createQueryBuilder();

        $qb->update('s_order')
            ->set('transactionID', $qb->expr()->literal($transactionID))
            ->where('ordernumber = :number')
            ->setParameter('number', $this->getOrderNumber())
            ->execute();
    }

    /**
     * @param array $data
     */
    protected function responseJSON(array $data)
    {
        try {
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();
            $this->Response()->setHeader('Content-type', 'application/json', true);
            $this->response->setBody(json_encode($data));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            echo json_encode($data, JSON_PRETTY_PRINT);
            die;
        }
    }

    /**
     * Create transaction in tPay
     *
     * @param array $transactionConfig
     *
     * @return array
     */
    protected function createTransaction(array $transactionConfig): array
    {
        try {
            $tpayTransaction = $this->tpayApi->create($transactionConfig);
            $this->transactionID = $tpayTransaction['title'];
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return ['status' => -1];
        }

        return $tpayTransaction;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function buildURL(array $data): string
    {
        return $this->container->get('router')->assemble($data);
    }

    /**
     * @return false|string
     */
    protected function errorReturn()
    {
        return $this->buildURL(['controller' => 'checkout', 'action' => 'finish', 'tpay' => 'error']);
    }

    /**
     * @return string
     */
    private function getNotificationUrl()
    {
        return $this->buildURL(['controller' => 'TpayPaymentWebhook', 'action' => 'notify', 'forceSecure' => true]);
    }

    /**
     * @return string
     */
    private function getReturnUrl()
    {
        return $this->buildURL(['controller' => 'checkout', 'action' => 'finish']);
    }
}
