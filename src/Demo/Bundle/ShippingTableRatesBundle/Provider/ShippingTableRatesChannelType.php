<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Provider;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;

class ShippingTableRatesChannelType implements ChannelInterface
{
    const TYPE = 'shipping_table_rates';

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'demo.shippingtablerates.channel_type.label';
    }
}
