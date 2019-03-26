<?php

namespace Demo\Bundle\ProductTitleBundle\Layout\DataProvider;

use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\WebCatalogBundle\Layout\DataProvider\TitleDataProviderInterface;

class ProductTitleDataProvider implements TitleDataProviderInterface
{
    /** @var TitleDataProviderInterface */
    private $decoratedProvider;

    /** @var LocalizationHelper */
    private $localizationHelper;

    public function __construct(TitleDataProviderInterface $decoratedProvider, LocalizationHelper $localizationHelper)
    {
        $this->decoratedProvider = $decoratedProvider;
        $this->localizationHelper = $localizationHelper;
    }

    public function getNodeTitle($default = '')
    {
        return $this->decoratedProvider->getNodeTitle($default);
    }

    public function getTitle($default = '', $data = null)
    {
        if ($data instanceof Product) {
            $brandName = $data->getBrand() ? $this->localizationHelper->getLocalizedValue($data->getBrand()->getNames()) : '';
            $productName = $this->localizationHelper->getLocalizedValue($data->getNames());
            $familyLabel = $this->localizationHelper->getLocalizedValue($data->getAttributeFamily()->getLabels());
            return $productName . ($brandName ? ' - ' . $brandName : '') . ' - ' . $familyLabel;
        }
        return $this->decoratedProvider->getTitle($default, $data);
    }
}
