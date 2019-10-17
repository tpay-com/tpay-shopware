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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use TpayShopwarePayments\Installer\Payments\BankTransfer;
use TpayShopwarePayments\Installer\Payments\Blik;
use TpayShopwarePayments\Installer\Payments\Card;

/**
 * Recognizes whether the payment provided is supported by this plugin
 *
 * Class PaymentRecognition
 */
class PaymentRecognition implements PaymentRecognitionInterface
{
    /** @var string */
    protected $pluginName;

    /** @var Connection */
    protected $connection;

    /** @var int */
    protected $blikID;

    /** @var int */
    protected $cardID;

    /** @var int */
    protected $bankTransferID;

    /**
     * PaymentRecognition constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isTpay(int $paymentID): bool
    {
        return $this->isBlik($paymentID) || $this->isCard($paymentID) || $this->isBankTransfer($paymentID);
    }

    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isBlik(int $paymentID): bool
    {
        if (empty($this->blikID)) {
            $this->blikID = $this->getIdByName((new Blik())->getName());
        }

        return $this->blikID === $paymentID;
    }

    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isCard(int $paymentID): bool
    {
        if (empty($this->cardID)) {
            $this->cardID = $this->getIdByName((new Card())->getName());
        }

        return $this->cardID === $paymentID;
    }

    /**
     * @param int $paymentID
     *
     * @return bool
     */
    public function isBankTransfer(int $paymentID): bool
    {
        if (empty($this->bankTransferID)) {
            $this->bankTransferID = $this->getIdByName((new BankTransfer())->getName());
        }

        return $this->bankTransferID === $paymentID;
    }

    /**
     * @param string $name
     *
     * @return int
     */
    private function getIdByName(string $name): int
    {
        return (int) $this->connection
            ->createQueryBuilder()
            ->select('id')
            ->from('s_core_paymentmeans')
            ->where('name = :name')
            ->setParameter('name', $name)
            ->execute()
            ->fetchColumn();
    }
}
