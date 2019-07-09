<?php
namespace TpayShopwarePayments\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use TpayShopwarePayments\TpayShopwarePayments as PluginBootstrap;

class PaymentFinderService implements PaymentFinderInterface
{
    /** @var Connection */
    private $connection;

    /**
     * PaymentFinderService constructor.
     * @param DriverConnection $connection
     */
    public function __construct(DriverConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return int
     */
    public function getMyPaymentID()
    {
        return (int)$this->connection
            ->createQueryBuilder()
            ->select('id')
            ->from('s_core_paymentmeans')
            ->where('name = :shortName')
            ->setParameter('shortName', PluginBootstrap::PAYMENT_SHORT_NAME)
            ->execute()
            ->fetchColumn();
    }

}
