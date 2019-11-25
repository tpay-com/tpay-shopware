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

namespace TpayShopwarePayments\Components\TpayPayment;

/**
 * Class TpayFactory
 */
class TpayFactory
{
    /** @var TpayConfigInterface $pluginConfig */
    private $pluginConfig;

    /**
     * TpayFactory constructor.
     *
     * @param TpayConfigInterface $pluginConfig
     * @param string              $pluginDir
     */
    public function __construct(TpayConfigInterface $pluginConfig, $pluginDir)
    {
        if (file_exists($pluginDir . '/vendor/autoload.php')) {
            require_once $pluginDir . '/vendor/autoload.php';
        }
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * @return TpayBasicApi
     */
    public function createBasicApi(): TpayBasicApi
    {
        return new TpayBasicApi(
            $this->pluginConfig->getMerchantID(),
            $this->pluginConfig->getMerchantSecret(),
            $this->pluginConfig->getTransactionApiKey(),
            $this->pluginConfig->getTransactionApiPassword()
        );
    }

    /**
     * @return TpayBasicForm
     */
    public function createBasicForm(): TpayBasicForm
    {
        return new TpayBasicForm(
            $this->pluginConfig->getMerchantID(),
            'xxx'
        );
    }

    /**
     * @return TpayBasicNotificationHandler
     */
    public function createTransactionNotificationHandler(): TpayBasicNotificationHandler
    {
        return new TpayBasicNotificationHandler(
            $this->pluginConfig->getMerchantID(),
            $this->pluginConfig->getMerchantSecret()
        );
    }
}
