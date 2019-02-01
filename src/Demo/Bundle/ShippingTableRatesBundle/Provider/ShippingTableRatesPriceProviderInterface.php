<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Provider;

use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;

interface ShippingTableRatesPriceProviderInterface
{
    /**
     * @param ShippingContextInterface $context
     *
     * @return float
     */
    public function getPrice(ShippingContextInterface $context);

    /**
     * @param ShippingContextInterface $context
     *
     * @return float
     */
    public function getHandlingFee(ShippingContextInterface $context);
}
