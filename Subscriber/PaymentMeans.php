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

namespace TpayShopwarePayments\Subscriber;

use Enlight\Event\SubscriberInterface;
use TpayShopwarePayments\Service\BankList;
use TpayShopwarePayments\Service\BankListInterface;
use TpayShopwarePayments\Service\PaymentRecognition;
use TpayShopwarePayments\Service\PaymentRecognitionInterface;

/**
 * Class PaymentMeans
 */
class PaymentMeans implements SubscriberInterface
{
    /** @var PaymentRecognition */
    private $paymentRecognition;

    /** @var BankList */
    private $bankList;

    /**
     * PaymentMeans constructor.
     *
     * @param PaymentRecognitionInterface $paymentRecognition
     * @param BankListInterface           $bankList
     */
    public function __construct(PaymentRecognitionInterface $paymentRecognition, BankListInterface $bankList)
    {
        $this->paymentRecognition = $paymentRecognition;
        $this->bankList = $bankList;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return ['Shopware_Modules_Admin_GetPaymentMeans_DataFilter' => 'onPaymentDataFilter'];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     *
     * @return array
     */
    public function onPaymentDataFilter(\Enlight_Event_EventArgs $args)
    {
        $payments = $args->getReturn();

        foreach ($payments as &$payment) {
            $payment['tPayDetail'] = [
                'blik' => $this->paymentRecognition->isBlik($payment['id']),
                'card' => $this->paymentRecognition->isCard($payment['id']),
                'bankTransfer' => $this->paymentRecognition->isBankTransfer($payment['id']),
            ];
            $payment['isTpay'] = $this->paymentRecognition->isTpay($payment['id']);
            if ($payment['tPayDetail']['bankTransfer']) {
                $payment['tPayBankList'] = $this->bankList->getCachedList();
            }
        }

        return $payments;
    }
}
