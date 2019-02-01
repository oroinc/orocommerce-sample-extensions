<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Method;

use Oro\Bundle\ShippingBundle\Method\ShippingMethodIconAwareInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodInterface;
use Demo\Bundle\ShippingTableRatesBundle\Provider\ShippingTableRatesPriceProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ShippingTableRatesMethod implements ShippingMethodInterface, ShippingMethodIconAwareInterface
{
    /** @var ShippingTableRatesMethodType */
    private $type;

    /** @var string */
    private $label;

    /** @var string */
    private $identifier;

    /** @var bool */
    private $enabled = false;

    /**
     * @param ShippingTableRatesPriceProviderInterface $priceProvider
     * @param string $identifier
     * @param string $label
     * @param bool $enabled
     */
    public function __construct(ShippingTableRatesPriceProviderInterface $priceProvider, $identifier, $label, $enabled)
    {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->type = new ShippingTableRatesMethodType($priceProvider, $this->identifier, $label);
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function isGrouped()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [$this->type];
    }

    /**
     * {@inheritdoc}
     */
    public function getType($type)
    {
        if ($this->type->getIdentifier() === $type) {
            return $this->type;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsConfigurationFormType()
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return 20;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return null;
    }
}
