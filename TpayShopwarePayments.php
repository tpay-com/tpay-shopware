<?php
namespace TpayShopwarePayments;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Payment\Payment;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TpayShopwarePayments\Components\Installer\PluginInstaller;

class TpayShopwarePayments extends Plugin
{
    const PLUGIN_NAME = 'TpayShopwarePayments';

    const PLUGIN_VERSION = '1.0.0';

    const LOCALE_AVAILABLE = ['en', 'pl'];

    const DEFAULT_LOCALE = 'pl';

    const PAYMENT_SHORT_NAME = 'tpay_shopware_payments';

    const BLIK_PAYMENT_SHORT_NAME = 'tpay_blik_shopware_payments';

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        /** @var \Shopware\Components\Plugin\PaymentInstaller $installer */
        $installer = $this->container->get('shopware.plugin_payment_installer');
        $paymentMethods = [
            [
                'name' => static::PAYMENT_SHORT_NAME,
                'description' => 'Tpay.com fast online payments',
                'action' => 'TpayPayment',
                'active' => 0,
                'position' => 0,
                'additionalDescription' =>
                    '<img src="https://tpay.com/img/banners/tpay-160x75.svg"/>'.
                    '<div id="payment_desc">'.
                    'Pay save and secure by online bank transfers or international payment methods via Tpay.com system.'.
                    '</div>',
            ],
            [
                'name' => static::BLIK_PAYMENT_SHORT_NAME,
                'description' => 'Fast BLIK payment by Tpay.com',
                'action' => 'TpayPayment',
                'active' => 0,
                'position' => 0,
                'additionalDescription' =>
                    '<img src="https://secure.tpay.com/_/banks/b64.png"/>'.
                    '<div id="payment_desc">'.
                    'Pay by BLIK method via secure Tpay.com online payments system'.
                    '</div>',
            ],
        ];
        foreach ($paymentMethods as $paymentMethodDetails) {
            $installer->createOrUpdate($context->getPlugin(), $paymentMethodDetails);
        }
        $this->installSchema();
        $context->scheduleClearCache($context::CACHE_LIST_ALL);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
    }

    /**
     * @param DeactivateContext $context
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), true);
    }

    /**
     * @param ContainerBuilder $containerBuilder
     *
     * @return void
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->setParameter('tpay_shopware.plugin_name', self::PLUGIN_NAME);
        $containerBuilder->setParameter('tpay_shopware.plugin_version', self::PLUGIN_VERSION);
        $containerBuilder->setParameter('tpay_shopware_payments.plugin_dir', $this->getPath());
        $containerBuilder->setParameter('tpay_shopware_payments.template_dir', $this->getPath().'/Resources/views/');

        parent::build($containerBuilder);
    }

    /**
     * @param Payment[] $payments
     * @param $active bool
     */
    private function setActiveFlag($payments, $active)
    {
        $em = $this->container->get('models');

        foreach ($payments as $payment) {
            $payment->setActive($active);
        }
        $em->flush();
    }

    private function installSchema()
    {
        $pluginInstaller = new PluginInstaller($this->container->get('models'));
        $pluginInstaller->createOrUpdateSchema();
    }

}
