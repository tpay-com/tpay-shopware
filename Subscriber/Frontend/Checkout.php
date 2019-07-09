<?php
namespace TpayShopwarePayments\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use TpayShopwarePayments\Components\TpayPayment\TpayConfigInterface;
use TpayShopwarePayments\Components\TpayPayment\TpayConfigService;
use TpayShopwarePayments\Service\PaymentFinderInterface;
use TpayShopwarePayments\Service\PaymentFinderService;

class Checkout implements SubscriberInterface
{

    /** @var string $templateDir */
    protected $templateDir;

    /** @var PaymentFinderService */
    protected $paymentFinder;

    /** @var TpayConfigService */
    protected $pluginConfig;

    /** @var \Shopware_Components_Snippet_Manager */
    protected $snippets;

    public function __construct(
        $templateDir,
        PaymentFinderInterface $paymentFinder,
        TpayConfigInterface $pluginConfig,
        $snippets
    ) {
        $this->templateDir = $templateDir;
        $this->paymentFinder = $paymentFinder;
        $this->pluginConfig = $pluginConfig;
        $this->snippets = $snippets;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return ['Enlight_Controller_Action_PreDispatch_Frontend_Checkout' => 'onCheckout'];
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     *
     * @return void
     */
    public function onCheckout(Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $request = $args->getSubject()->Request();

        if ($request->getActionName() === 'confirm' && $request->has('tpayErr')) {
            $namespace = $this->snippets->getNamespace('frontend/plugins/tpay');
            $view->assign(
                'sBasketInfo',
                $namespace->get(
                    'unsupported_currency',
                    'Wybrana metoda płatnosci nie wspiera wybranej waluty. Proszę zmienić walutę lub metodę płątności.'
                )
            );
        }
    }

}
