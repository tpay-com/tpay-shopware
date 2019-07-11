<?php
namespace TpayShopwarePayments\Components\TpayPayment;

use Psr\Log\LoggerInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\ConfigReader;
use Shopware\Models\Shop\Shop;

class TpayConfigService implements TpayConfigInterface
{
    /** @var array */
    private $config;

    /**
     * TpayConfigService constructor.
     * @param ConfigReader $cachedConfigReader
     * @param ContextServiceInterface $contextService
     * @param ModelManager $modelManager
     * @param LoggerInterface $logger
     * @param string $pluginName
     */
    public function __construct(
        ConfigReader $cachedConfigReader,
        ContextServiceInterface $contextService,
        ModelManager $modelManager,
        LoggerInterface $logger,
        $pluginName
    ) {
        try {
            $shop = $modelManager->find(Shop::class, $contextService->getShopContext()->getShop()->getId());
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
            $shop = null;
        }
        $this->config = $cachedConfigReader->getByPluginName($pluginName, $shop);
    }

    /**
     * Merchant ID
     *
     * @return int
     */
    public function getMerchantID()
    {
        return (int)$this->config['tpay_merchant_id'];
    }

    /**
     * Merchant secret
     *
     * @return string
     */
    public function getMerchantSecret()
    {
        return $this->config['tpay_merchant_secret'];
    }

    /**
     * Transaction API key
     *
     * @return string
     */
    public function getTransactionApiKey()
    {
        return $this->config['tpay_merchant_tr_api_key'];
    }

    /**
     * Transaction API key password
     *
     * @return string
     */
    public function getTransactionApiPassword()
    {
        return $this->config['tpay_merchant_tr_api_pass'];
    }

    /**
     * Send order status change email setting
     *
     * @return bool
     */
    public function getSendStatusChangeEmail()
    {
        return $this->config['tpay_send_status_change_email'];
    }

}
