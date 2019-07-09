<?php
namespace TpayShopwarePayments\Service;

use Enlight_Components_Session_Namespace;
use Shopware\Components\Model\ModelManager;
use TpayShopwarePayments\Components\Model\PaymentDetails;

class UserPaymentMethodService implements UserPaymentMethodInterface
{
    /** @var Enlight_Components_Session_Namespace */
    protected $session;

    /** @var ModelManager */
    protected $modelManager;

    public function __construct(Enlight_Components_Session_Namespace $session, ModelManager $modelManager)
    {
        $this->session = $session;
        $this->modelManager = $modelManager;
    }

    public function saveUserPayment($paymentMethodID, $blikCode = '')
    {
        $userID = $this->session->get('sUserId');
        $detail = $this->modelManager->getRepository(PaymentDetails::class)->findOneBy(['userId' => $userID]);
        if (empty($detail)) {
            $detail = new PaymentDetails();
            $detail->setUserId((int)$userID);
        }
        $detail->setCardData('')
            ->setGroupId((int)$paymentMethodID)
            ->setBlikCode($blikCode)
            ->setCreatedAt(new \DateTime());

        $this->modelManager->persist($detail);
        try {
            $this->modelManager->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function getUserGroupId()
    {
        $userID = $this->session->get('sUserId');
        /** @var PaymentDetails $detail */
        $detail = $this->modelManager->getRepository(PaymentDetails::class)->findOneBy(['userId' => $userID]);
        if (empty($detail)) {
            return 0;
        }

        return (int)$detail->getGroupId();
    }

    public function getUserBlikCode()
    {
        $userID = $this->session->get('sUserId');
        /** @var PaymentDetails $detail */
        $detail = $this->modelManager->getRepository(PaymentDetails::class)->findOneBy(['userId' => $userID]);
        if (empty($detail)) {
            return 0;
        }

        return $detail->getBlikCode();
    }

}
