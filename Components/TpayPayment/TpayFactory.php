<?php
namespace TpayShopwarePayments\Components\TpayPayment;

class TpayFactory
{
    /** @var TpayConfigInterface $pluginConfig */
    private $pluginConfig;

    /**
     * TpayFactory constructor.
     *
     * @param TpayConfigInterface $pluginConfig
     * @param string $pluginDir
     */
    public function __construct(TpayConfigInterface $pluginConfig, $pluginDir)
    {
        if (file_exists($pluginDir.'/vendor/autoload.php')) {
            require_once $pluginDir.'/vendor/autoload.php';
        }
        $this->pluginConfig = $pluginConfig;
    }

    public function createBasicApi()
    {
        return new TpayBasicApi(
            $this->pluginConfig->getMerchantID(),
            $this->pluginConfig->getMerchantSecret(),
            $this->pluginConfig->getTransactionApiKey(),
            $this->pluginConfig->getTransactionApiPassword()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBasicForm()
    {
        return new TpayBasicForm(
            $this->pluginConfig->getMerchantID(),
            'xxx'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createTransactionNotificationHandler()
    {
        return new TpayBasicNotificationHandler(
            $this->pluginConfig->getMerchantID(),
            $this->pluginConfig->getMerchantSecret()
        );
    }

}
