<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Method;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Oro\Bundle\ShippingBundle\Context\ShippingLineItemInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;
use Demo\Bundle\ShippingTableRatesBundle\Form\Type\ShippingTableRatesOptionsType;
use Demo\Bundle\ShippingTableRatesBundle\Provider\ShippingTableRatesPriceProviderInterface;

class ShippingTableRatesMethodType implements ShippingMethodTypeInterface
{
    const PER_ORDER_TYPE = 'per_order';
    const PER_ITEM_TYPE = 'per_item';

    /** @var ShippingTableRatesPriceProviderInterface */
    private $priceProvider;

    /** @var string */
    private $identifier;

    /** @var string */
    private $label;

    /**
     * @param ShippingTableRatesPriceProviderInterface $priceProvider
     * @param string $identifier
     * @param string $label
     */
    public function __construct(ShippingTableRatesPriceProviderInterface $priceProvider, $identifier, $label)
    {
        $this->priceProvider = $priceProvider;
        $this->identifier = $identifier;
        $this->label = $label;
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
    public function getLabel()
    {
        return $this->label;
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
    public function getOptionsConfigurationFormType()
    {
        return ShippingTableRatesOptionsType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(ShippingContextInterface $context, array $methodOptions, array $typeOptions)
    {
        $price = $this->priceProvider->getPrice($context);
        switch ($typeOptions['type']) {
            case static::PER_ORDER_TYPE:
                break;
            case static::PER_ITEM_TYPE:
                $countItems = array_sum(array_map(function (ShippingLineItemInterface $item) {
                    return $item->getQuantity();
                }, $context->getLineItems()->toArray()));
                $price = $countItems * $price;
                break;
            default:
                return null;
        }

        if ($price !== null) {
            $handlingFee = $this->priceProvider->getHandlingFee($context);

            return Price::create($price + $handlingFee, $context->getCurrency());
        }

        return null;
    }
}
