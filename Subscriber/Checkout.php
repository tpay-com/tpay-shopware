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

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Request_RequestHttp;
use Enlight_View_Default;
use Shopware_Components_Snippet_Manager as Snippets;
use TpayShopwarePayments\Service\BankSelection;
use TpayShopwarePayments\Service\BankSelectionInterface;
use TpayShopwarePayments\Service\PaymentRecognition;
use TpayShopwarePayments\Service\PaymentRecognitionInterface;

class Checkout implements SubscriberInterface
{
    /** @var BankSelection */
    private $bankSelection;

    /** @var PaymentRecognition $paymentRecognition */
    private $paymentRecognition;

    /** @var Snippets */
    private $snippetManager;

    /** @var Connection */
    private $connection;

    /**
     * Checkout constructor.
     *
     * @param BankSelectionInterface      $bankSelection
     * @param PaymentRecognitionInterface $paymentRecognition
     * @param Snippets                    $snippetManager
     * @param Connection                  $connection
     */
    public function __construct(BankSelectionInterface $bankSelection, PaymentRecognitionInterface $paymentRecognition, Snippets $snippetManager, Connection $connection)
    {
        $this->bankSelection = $bankSelection;
        $this->paymentRecognition = $paymentRecognition;
        $this->snippetManager = $snippetManager;
        $this->connection = $connection;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return  ['Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onCheckout'];
    }

    public function onCheckout(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Checkout $subject */
        $subject = $args->getSubject();

        /** @var Enlight_View_Default $view */
        $view = $subject->View();

        $view->assign('tpayBank', $this->bankSelection->currentUserSelected());

        $sPayment = $view->getAssign('sPayment');

        if (empty($sPayment)) {
            return;
        }

        $id = (int) $sPayment['id'];

        $sPayment['isTpayBlik'] = $this->paymentRecognition->isBlik($id);
        $sPayment['isTpayBankTransfer'] = $this->paymentRecognition->isBankTransfer($id);

        $view->assign('sPayment', $sPayment);

        $request = $subject->Request();
        if ($request->getActionName() === 'finish') {
            $this->extendFinishAction($view, $request);
        }
    }

    public function extendFinishAction(Enlight_View_Default $view, Enlight_Controller_Request_RequestHttp $request)
    {
        $number = $view->getAssign('sOrderNumber');

        $orderDetails = $this->connection
            ->createQueryBuilder()
            ->select('paymentID, cleared')
            ->from('s_order')
            ->where('ordernumber = :number')
            ->setParameter('number', $number)
            ->execute()
            ->fetch();

        $paymentID = (int) $orderDetails['paymentID'];

        if (!$this->paymentRecognition->isBlik($paymentID)) {
            return;
        }

        $statusDetails = $this->connection
            ->createQueryBuilder()
            ->select('name, description')
            ->from('s_core_states')
            ->where('id = :id')
            ->setParameter('id', $orderDetails['cleared'])
            ->execute()
            ->fetch();

        $status = $this->snippetManager->getNamespace('backend/static/payment_status')->get(
            $statusDetails['name'],
            $statusDetails['description']
        );

        $view->assign('orderStatus', $status);

        if ($request->get('tpay') === 'error') {
            $view->assign('tpayError', true);
        }
    }
}
