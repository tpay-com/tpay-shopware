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

/**
 * Class Shopware_Controllers_Frontend_TpayBankSelection
 */
class Shopware_Controllers_Frontend_TpayBankSelection extends \Enlight_Controller_Action
{
    /** @var \TpayShopwarePayments\Service\BankSelection */
    private $bankSelection;

    /**
     * @throws Exception
     */
    public function preDispatch()
    {
        $this->bankSelection = $this->container->get('tpay_shopware_payments.service.bank_selection');
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->Response()->setHeader('Content-type', 'application/json', true);
    }

    /**
     * Save selected Bank in User Attribute
     */
    public function indexAction()
    {
        $id = (int) $this->request->get('id');
        $name = $this->request->get('name');
        $img = $this->request->get('img');

        $success = $this->bankSelection->saveUser($id, $name, $img);

        $this->response->setBody(json_encode([
            'success' => $success,
        ]));
    }
}
