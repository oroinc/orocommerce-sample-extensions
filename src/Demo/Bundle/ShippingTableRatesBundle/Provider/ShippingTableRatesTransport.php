<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Provider;

use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Demo\Bundle\ShippingTableRatesBundle\Form\Type\ShippingTableRatesSettingsType;
use Demo\Bundle\ShippingTableRatesBundle\Entity\ShippingTableRatesSettings;

class ShippingTableRatesTransport implements TransportInterface
{
    /**
     * {@inheritdoc}
     */
    public function init(Transport $transportEntity)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'demo.shippingtablerates.transport.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return ShippingTableRatesSettingsType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEntityFQCN()
    {
        return ShippingTableRatesSettings::class;
    }
}
