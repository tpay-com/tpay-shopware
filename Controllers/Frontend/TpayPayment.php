<?php

use tpayLibs\src\_class_tpay\Utilities\TException;
use TpayShopwarePayments\Components\TpayPayment\TpayBasicApi;
use Shopware\Components\CSRFWhitelistAware;
use tpayLibs\src\_class_tpay\Utilities\Util;
use WhiteCube\Lingua\Service as Lingua;
use TpayShopwarePayments\TpayShopwarePayments as PluginBootstrap;

class Shopware_Controllers_Frontend_TpayPayment extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{
    const PAYMENT_STATUS_OPEN = 17;

    const AVAILABLE_PAYMENTS = [
        PluginBootstrap::BLIK_PAYMENT_SHORT_NAME,
        PluginBootstrap::PAYMENT_SHORT_NAME,
    ];

    /**
     * @var TpayBasicApi
     */
    protected $tpayApi;

    /** @var TpayShopwarePayments\Components\TpayPayment\TpayBasicForm */
    protected $basicForm;

    /** @var Psr\Log\LoggerInterface */
    protected $logger;

    /** @var Shopware\Components\Routing\Router */
    protected $router;

    /**
     * @var null|int
     */
    private $currentOrderId;

    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
        ];
    }

    public function preDispatch()
    {
        $this->container
            ->get('template')
            ->addTemplateDir($this->container->getParameter('tpay_shopware_payments.template_dir'));
        $this->tpayApi = $this->container->get('tpay_shopware_payments.basic_api');
        $this->basicForm = $this->container->get('tpay_shopware_payments.basic_form');
        $this->logger = $this->container->get('tpaylogger');
        $this->router = $this->container->get('router');
    }

    /**
     * Index action method.
     *
     * Forwards to the correct action.
     *
     * @throws Exception
     */
    public function indexAction()
    {
        $currency = $this->getCurrencyShortName();
        // Check if one of the payment methods is selected and currency is supported. Else return to default controller.
        if (!in_array($this->getPaymentShortName(), static::AVAILABLE_PAYMENTS) || $currency !== 'PLN') {
            $this->logger->error(
                sprintf(
                    'Unsupported currency or payment method. Accepted payment method: \'tpay_shopware_payments\' given %s Accepted currency \'PLN\' given %s',
                    $this->getPaymentShortName(),
                    $currency
                )
            );

            return $this->redirect([
                'controller' => 'checkout',
                'action' => 'confirm',
                'tpayErr' => 1,
            ]);
        }
        $util = new Util();
        $util->setLanguage($this->getLanguage());
        if ($this->getPaymentShortName() === PluginBootstrap::PAYMENT_SHORT_NAME) {
            $this->View()
                ->assign('tpayForm', $this->basicForm->getSimpleBankList(false, false))
                ->assign('paymentType', 'basic');
        }
        if ($this->getPaymentShortName() === PluginBootstrap::BLIK_PAYMENT_SHORT_NAME) {
            $this->View()
                ->assign('tpayForm', $this->basicForm->getBlikSelectionForm(''))
                ->assign('paymentType', 'blik');
        }

    }

    public function changePaymentMethodAction()
    {
        $groupId = '';
        $blikCode = '';
        if ($this->request->has('id')) {
            $groupId = $this->request->get('id');
        }
        if ($this->request->has('blikCode')) {
            $blikCode = $this->request->get('blikCode');
        }
        $service = $this->container->get('tpay_shopware_payments.service.user_payment_method_service');
        $status = $service->saveUserPayment($groupId, $blikCode);
        $this->front->Plugins()->ViewRenderer()->setNoRender();
        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody(json_encode(['success' => $status]));
    }

    /**
     * Collects the payment information and transmits it to the payment provider.
     *
     * @throws Exception
     */
    public function basicAction()
    {
        $transactionConfig = $this->getTransactionConfig();
        try {
            $tpayTransaction = $this->tpayApi->create($transactionConfig);
            $this->updateOrderTransactionTitle($tpayTransaction['title']);
        } catch (TException $TException) {
            $this->logger->error('TException '.$TException->getMessage());

            return $this->redirect(['controller' => 'checkout']);
        } catch (Exception $e) {
            $this->logger->error('Exception'.$e->getMessage());

            return $this->redirect(['controller' => 'checkout']);
        }

        return $this->redirect($tpayTransaction['url']);
    }

    /**
     * Collects the payment information and transmits it to the payment provider.
     *
     * @throws Exception
     */
    public function blikAction()
    {
        $userPaymentService = $this->container->get('tpay_shopware_payments.service.user_payment_method_service');
        $blikCode = $userPaymentService->getUserBlikCode();
        $transactionConfig = $this->getTransactionConfig();
        $transactionConfig['group'] = 150;
        try {
            $tpayTransaction = $this->tpayApi->create($transactionConfig);
            $this->updateOrderTransactionTitle($tpayTransaction['title']);
        } catch (TException $TException) {
            $this->logger->error('TException '.$TException->getMessage());

            return $this->redirect(['controller' => 'checkout']);
        } catch (Exception $e) {
            $this->logger->error('Exception'.$e->getMessage());

            return $this->redirect(['controller' => 'checkout']);
        }
        try {
            $apiResult = $this->tpayApi->blik($tpayTransaction['title'], $blikCode);
            if (isset($apiResult['result']) && $apiResult['result'] === 1) {
                return $this->redirect($transactionConfig['return_url']);
            }
        } catch (TException $TException) {
            $this->logger->error('TException '.$TException->getMessage());
        }

        return $this->redirect($tpayTransaction['url']);
    }

    /**
     * @param string $transactionTitle
     */
    private function updateOrderTransactionTitle($transactionTitle)
    {
        $sql = '
            UPDATE s_order
            SET transactionID = ?
            WHERE ordernumber = ?
        ';
        Shopware()->Db()->executeQuery($sql, [
            $transactionTitle,
            $this->currentOrderId,
        ]);
    }

    /**
     * @return array
     */
    private function getTransactionConfig()
    {
        $user = $this->getUser();
        $billing = $user['billingaddress'];
        $crc = $this->createPaymentUniqueId();
        $this->insertOrder($crc, $crc);
        $userPaymentService = $this->container->get('tpay_shopware_payments.service.user_payment_method_service');
        $userPaymentID = $userPaymentService->getUserGroupId();
        $parameter = [
            'amount' => $this->getAmount(),
            'crc' => $crc,
            'name' => $billing['firstname'].' '.$billing['lastname'],
            'city' => $billing['city'],
            'address' => $billing['street'],
            'zip' => $billing['zipcode'],
            'return_url' => $this->getReturnUrl(),
            'return_error_url' => $this->getReturnUrl(),
            'result_url' => $this->getNotificationUrl(),
            'description' => 'Zamówienie nr '.$this->currentOrderId,
            'email' => $user['additional']['user']['email'],
            'language' => 'PL',
            'module' => 'Shopware',
        ];
        if ($userPaymentID > 0) {
            $parameter['group'] = $userPaymentID;
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
     * @param string $paymentUniqueId Additional random Id passed to Tpay
     */
    private function insertOrder($tpayTransactionId, $paymentUniqueId)
    {
        $this->currentOrderId = $this->saveOrder(
            $tpayTransactionId,
            $paymentUniqueId,
            static::PAYMENT_STATUS_OPEN
        );
    }

    /**
     * @return string
     */
    private function getNotificationUrl()
    {
        return $this->router->assemble([
            'controller' => 'TpayPaymentWebhook',
            'action' => 'notify',
            'forceSecure' => true,
        ]);
    }

    /**
     * @return string
     */
    private function getReturnUrl()
    {
        return $this->router->assemble(['controller' => 'checkout', 'action' => 'finish']);
    }

    /**
     * string
     */
    private function getLanguage()
    {
        $context = $this->container->get('shopware_storefront.context_service');
        $locale = $context->getShopContext()->getShop()->getLocale()->getLocale();
        $language = Lingua::createFromPHP($locale);
        $lang = $language->toISO_639_1();
        if (in_array($lang, PluginBootstrap::LOCALE_AVAILABLE)) {
            return $lang;
        }

        return PluginBootstrap::DEFAULT_LOCALE;
    }
}
