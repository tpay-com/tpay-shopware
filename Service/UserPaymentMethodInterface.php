<?php
namespace TpayShopwarePayments\Service;

interface UserPaymentMethodInterface
{
    /**
     * Saves the payment method chosen by the customer in the database.
     *
     * @param integer $paymentMethodID
     * @param string $blikCode
     * @return bool
     */
    public function saveUserPayment($paymentMethodID, $blikCode);

    /**
     * Returns the payment method chosen by the client from the database. Or zero if the customer did not choose any.
     *
     * @return integer
     */
    public function getUserGroupId();

}
