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

namespace TpayShopwarePayments\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use TpayShopwarePayments\Components\TpayPayment\TpayConfig;
use TpayShopwarePayments\Components\TpayPayment\TpayConfigInterface;
use TpayShopwarePayments\TpayShopwarePayments as Plugin;
use Zend_Cache_Core as Cache;

/**
 * The class retrieves a list of available payment channels from the tpay API
 *
 * Class BankList
 */
class BankList implements BankListInterface
{
    const ENDPOINT = 'https://secure.tpay.com/';

    const EXCLUDED = [
        Plugin::BLIK,
        Plugin::CARD,
    ];

    /** @var Cache */
    private $cache;

    /** @var LoggerInterface */
    private $logger;

    /** @var TpayConfig */
    private $config;

    /**
     * BankList constructor.
     *
     * @param Cache               $cache
     * @param LoggerInterface     $logger
     * @param TpayConfigInterface $config
     */
    public function __construct(Cache $cache, LoggerInterface $logger, TpayConfigInterface $config)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getCachedList(): array
    {
        $cacheLifetime = 3600; //1h
        $cacheKey = 'TpayShopwarePayments_Bank_List';
        if ($this->cache->test($cacheKey)) {
            $cached = $this->cache->load($cacheKey);
            if (is_array($cached) && !empty($cached)) {
                return $cached;
            }
        }

        $list = $this->getList();
        if (!empty($list)) {
            try {
                $this->cache->save($list, $cacheKey, [], $cacheLifetime);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        $guzzle = new Client(['base_url' => self::ENDPOINT]);
        $url = 'groups-' . $this->config->getMerchantID() . $this->config->getChannelsType() . '.js?json';
        $response = $guzzle->get($url);
        $list = $response->json();
        foreach ($list as $key => $item) {
            if (in_array($item['id'], self::EXCLUDED)) {
                unset($list[$key]);
            }
        }

        return $list;
    }
}
