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

namespace TpayShopwarePayments;

use Doctrine\ORM\PersistentCollection;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\PaymentInstaller;
use Shopware\Components\Snippet\DatabaseHandler;
use Shopware\Models\Payment\Payment;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TpayShopwarePayments\Installer\PaymentsStruct;

/**
 * Class TpayShopwarePayments
 */
class TpayShopwarePayments extends Plugin
{
    const BLIK = 150;
    const CARD = 103;

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('tpay_shopware_payments.view_dir', $this->getPath() . '/Resources/views/');
        $container->setParameter('tpay_shopware_payments.snippets_dir', $this->getSnippetDir());
        parent::build($container);
    }

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        $this->createAttributes();
        $this->loadSnippets();
        $this->createPayments($context);
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
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function update(UpdateContext $context)
    {
        $this->loadSnippets();
        $context->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }

    /**
     * @param InstallContext $context
     */
    protected function createPayments(InstallContext $context)
    {
        $paymentsStruct = new PaymentsStruct();

        /** @var PaymentInstaller $installer */
        $installer = $this->container->get('shopware.plugin_payment_installer');

        foreach ($paymentsStruct->getAll($context->getPlugin()->getId(), true) as $payment) {
            $paymentInstance = $installer->createOrUpdate($context->getPlugin(), $payment);
            $this->createRule($paymentInstance->getId());
        }
    }

    /**
     * Creates Shopware rules blocking payments if the currency is not PLN
     *
     * @param int $paymentID
     */
    private function createRule(int $paymentID)
    {
        $connection = $this->container->get('dbal_connection');

        $old = $connection
            ->createQueryBuilder()
            ->select('id')
            ->from('s_core_rulesets')
            ->where('paymentID = :paymentID')
            ->andWhere("rule1 LIKE 'CURRENCIESISOISNOT'")
            ->andWhere("value1 LIKE 'PLN'")
            ->setParameter('paymentID', $paymentID)
            ->execute()
            ->fetchColumn();

        if (!empty($old)) {
            return;
        }

        $qb = $connection->createQueryBuilder();

        $data = [
            'paymentID' => $paymentID,
            'rule1' => $qb->expr()->literal('CURRENCIESISOISNOT'),
            'value1' => $qb->expr()->literal('PLN'),
        ];

        $qb->insert('s_core_rulesets')
            ->values($data)->execute();
    }

    /**
     * @param PersistentCollection $payments
     * @param bool                 $active
     */
    private function setActiveFlag(PersistentCollection $payments, bool $active)
    {
        $em = $this->container->get('models');

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            $payment->setActive($active);
        }
        try {
            $em->flush();
        } catch (\Exception $exception) {
            $this->container->get('pluginlogger')->error('tPay installer error: ' . $exception->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    private function createAttributes()
    {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_user_attributes', 'tpay_bank_id', TypeMapping::TYPE_INTEGER, [
            'custom' => false,
        ]);
        $service->update('s_user_attributes', 'tpay_bank_name', TypeMapping::TYPE_STRING, [
            'custom' => false,
        ]);
        $service->update('s_user_attributes', 'tpay_bank_logo', TypeMapping::TYPE_STRING, [
            'custom' => false,
        ]);
        $service->update('s_order_attributes', 'tpay_bank_id', TypeMapping::TYPE_INTEGER, [
            'custom' => false,
        ]);
        $service->update('s_order_attributes', 'tpay_bank_name', TypeMapping::TYPE_STRING, [
            'custom' => false,
        ]);
        $service->update('s_order_attributes', 'tpay_bank_logo', TypeMapping::TYPE_STRING, [
            'custom' => false,
        ]);
    }

    private function loadSnippets()
    {
        /** @var DatabaseHandler $databaseLoader */
        $databaseLoader = $this->container->get('shopware.snippet_database_handler');
        $databaseLoader->loadToDatabase($this->getSnippetDir(), false);
    }

    private function getSnippetDir()
    {
        return $this->getPath() . '/Resources/snippets/';
    }
}
