<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Factory;

use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\ShippingBundle\Method\Factory\IntegrationShippingMethodFactoryInterface;
use Demo\Bundle\ShippingTableRatesBundle\Entity\ShippingTableRatesSettings;
use Demo\Bundle\ShippingTableRatesBundle\Method\ShippingTableRatesMethod;
use Demo\Bundle\ShippingTableRatesBundle\Provider\ShippingTableRatesPriceProviderInterface;

class ShippingTableRatesMethodFactory implements IntegrationShippingMethodFactoryInterface
{
    /** @var IntegrationIdentifierGeneratorInterface */
    private $identifierGenerator;

    /** @var LocalizationHelper */
    private $localizationHelper;

    /** @var ShippingTableRatesPriceProviderInterface */
    private $priceProvider;

    /**
     * @param IntegrationIdentifierGeneratorInterface $identifierGenerator
     * @param LocalizationHelper $localizationHelper
     * @param ShippingTableRatesPriceProviderInterface $priceProvider
     */
    public function __construct(
        IntegrationIdentifierGeneratorInterface $identifierGenerator,
        LocalizationHelper $localizationHelper,
        ShippingTableRatesPriceProviderInterface $priceProvider
    ) {
        $this->identifierGenerator = $identifierGenerator;
        $this->localizationHelper = $localizationHelper;
        $this->priceProvider = $priceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Channel $channel)
    {
        $id = $this->identifierGenerator->generateIdentifier($channel);
        $label = $this->getChannelLabel($channel);

        return new ShippingTableRatesMethod($this->priceProvider, $id, $label, $channel->isEnabled());
    }

    /**
     * @param Channel $channel
     *
     * @return string
     */
    private function getChannelLabel(Channel $channel)
    {
        /** @var ShippingTableRatesSettings $transport */
        $transport = $channel->getTransport();

        return (string) $this->localizationHelper->getLocalizedValue($transport->getLabels());
    }
}
