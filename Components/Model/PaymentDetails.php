<?php
namespace TpayShopwarePayments\Components\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tpay_payment_details")
 */
class PaymentDetails
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="card_data", type="string", nullable=true)
     */
    private $cardData;

    /**
     * @var string
     *
     * @ORM\Column(name="blik_code", type="string", nullable=true)
     */
    private $blikCode;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return PaymentDetails
     */
    public function setUserId(int $userId): PaymentDetails
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     * @return PaymentDetails
     */
    public function setGroupId(int $groupId): PaymentDetails
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardData(): string
    {
        return $this->cardData;
    }

    /**
     * @param string $cardData
     * @return PaymentDetails
     */
    public function setCardData(string $cardData): PaymentDetails
    {
        $this->cardData = $cardData;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlikCode(): string
    {
        return $this->blikCode;
    }

    /**
     * @param string $blikCode
     * @return PaymentDetails
     */
    public function setBlikCode(string $blikCode): PaymentDetails
    {
        $this->blikCode = $blikCode;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return PaymentDetails
     */
    public function setCreatedAt(DateTime $createdAt): PaymentDetails
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
