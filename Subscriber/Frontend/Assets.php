<?php
namespace TpayShopwarePayments\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Theme\LessDefinition;

class Assets implements SubscriberInterface
{
    /**
     * @var string $view_dir
     */
    private $view_dir;

    public function __construct($view_dir)
    {
        $this->view_dir = $view_dir;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles',
            'Theme_Compiler_Collect_Plugin_Less' => 'addLessFiles',
        ];
    }

    /**
     * @return ArrayCollection
     * @Enlight\Theme_Compiler_Collect_Plugin_Javascript
     */
    public function addJsFiles()
    {
        $jsFiles = [
            $this->view_dir.'/frontend/_public/src/js/jquery.tpay.js',
        ];

        return new ArrayCollection($jsFiles);
    }

    /**
     * @return ArrayCollection
     * @Enlight\Theme_Compiler_Collect_Plugin_Less
     */
    public function addLessFiles()
    {
        $less = new LessDefinition(
            [],
            [
                $this->view_dir.'/frontend/_public/src/less/all.less',
            ]
        );

        return new ArrayCollection([$less]);
    }

}
