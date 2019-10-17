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

namespace TpayShopwarePayments\Subscriber;

use Enlight\Event\SubscriberInterface;

/**
 * Class Frontend
 */
class Frontend implements SubscriberInterface
{
    /** @var \Enlight_Template_Manager */
    protected $template;

    /** @var string */
    protected $viewDir;

    /**
     * Frontend constructor.
     *
     * @param \Enlight_Template_Manager $template
     * @param string                    $viewDir
     */
    public function __construct(\Enlight_Template_Manager $template, string $viewDir)
    {
        $this->template = $template;
        $this->viewDir = $viewDir;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onFrontendPostDispatch',
        ];
    }

    /**
     * Extend Template
     */
    public function onFrontendPostDispatch()
    {
        $this->template->addTemplateDir($this->viewDir);
    }
}
