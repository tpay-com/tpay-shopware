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

namespace TpayShopwarePayments\Installer\Payments;

/**
 * Class Payment
 */
abstract class Payment implements \JsonSerializable
{
    /**
     * Payment technical name
     *
     * @var string
     */
    protected $name;

    /**
     * Payment short description
     *
     * @var string
     */
    protected $description;

    /**
     * Payment Controller Name
     *
     * @var string
     */
    protected $action;

    /**
     * @var int
     */
    protected $active;

    /**
     * @var int
     */
    protected $position;

    /**
     * Long payment description
     *
     * @var string
     */
    protected $additionalDescription;

    /**
     * @var int
     */
    protected $pluginID;

    /**
     * The name of the TPL file that will be loaded in the payment selection page under the item.
     *
     * @var string
     */
    protected $template;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Payment
     */
    public function setName(string $name): Payment
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Payment
     */
    public function setDescription(string $description): Payment
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return Payment
     */
    public function setAction(string $action): Payment
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @param int $active
     *
     * @return Payment
     */
    public function setActive(int $active): Payment
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return Payment
     */
    public function setPosition(int $position): Payment
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalDescription(): string
    {
        return $this->additionalDescription;
    }

    /**
     * @param string $additionalDescription
     *
     * @return Payment
     */
    public function setAdditionalDescription(string $additionalDescription): Payment
    {
        $this->additionalDescription = $additionalDescription;

        return $this;
    }

    /**
     * @return int
     */
    public function getPluginID(): int
    {
        return $this->pluginID;
    }

    /**
     * @param int $pluginID
     *
     * @return Payment
     */
    public function setPluginID(int $pluginID): Payment
    {
        $this->pluginID = $pluginID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return Payment
     */
    public function setTemplate(string $template): Payment
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $vars = get_object_vars($this);
        foreach ($vars as $property => $value) {
            $vars[$property] = $value;
        }

        return array_filter($vars);
    }
}
