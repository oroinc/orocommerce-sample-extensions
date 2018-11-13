<?php

namespace Demo\Bundle\PricingBundle\EventListener;

use Oro\Bundle\PricingBundle\Model\DTO\ProductPriceDTO;
use Oro\Bundle\PricingBundle\Model\ProductPriceScopeCriteria;
use Oro\Bundle\PricingBundle\Placeholder\CurrencyPlaceholder;
use Oro\Bundle\PricingBundle\Placeholder\UnitPlaceholder;
use Oro\Bundle\PricingBundle\Provider\ProductPriceProviderInterface;
use Oro\Bundle\WebsiteSearchBundle\Event\IndexEntityEvent;
use Oro\Bundle\WebsiteSearchBundle\Manager\WebsiteContextManager;
use Oro\Bundle\WebsiteSearchBundle\Placeholder\WebsiteIdPlaceholder;

/**
 * Add prices information to product search index.
 * Only Website prices are used.
 */
class WebsiteSearchProductPriceIndexerListener
{
    /**
     * @var WebsiteContextManager
     */
    private $websiteContextManger;

    /**
     * @var ProductPriceProviderInterface
     */
    private $priceProvider;

    /**
     * @param WebsiteContextManager $websiteContextManager
     * @param ProductPriceProviderInterface $priceProvider
     */
    public function __construct(
        WebsiteContextManager $websiteContextManager,
        ProductPriceProviderInterface $priceProvider
    ) {
        $this->websiteContextManger = $websiteContextManager;
        $this->priceProvider = $priceProvider;
    }

    /**
     * @param IndexEntityEvent $event
     */
    public function onWebsiteSearchIndex(IndexEntityEvent $event)
    {
        $website = $this->websiteContextManger->getWebsite($event->getContext());
        if (!$website) {
            $event->stopPropagation();

            return;
        }

        $scopeCriteria = new ProductPriceScopeCriteria();
        $scopeCriteria->setWebsite($website);

        /**
         * Only test solution, must be rewrote for the real case
         */
        $websiteEnabledCurrencies = ['USD'];

        $prices = $this->priceProvider->getPricesByScopeCriteriaAndProducts(
            $scopeCriteria,
            $event->getEntities(),
            $websiteEnabledCurrencies
        );

        $minPrices = $this->getMinimalPrices($prices);
        foreach ($minPrices as $productId => $pricesByCurrency) {
            foreach ($pricesByCurrency as $currency => $pricesByUnit) {
                foreach ($pricesByUnit as $unit => $price) {
                    $event->addPlaceholderField(
                        $productId,
                        'minimal_price_WEBSITE_ID_CURRENCY_UNIT',
                        $price,
                        [
                            WebsiteIdPlaceholder::NAME => $website->getId(),
                            CurrencyPlaceholder::NAME => $currency,
                            UnitPlaceholder::NAME => $unit,
                        ]
                    );
                }

                $event->addPlaceholderField(
                    $productId,
                    'minimal_price_WEBSITE_ID_CURRENCY',
                    min($pricesByUnit),
                    [
                        WebsiteIdPlaceholder::NAME => $website->getId(),
                        CurrencyPlaceholder::NAME => $currency,
                    ]
                );
            }
        }
    }

    /**
     * @param array $prices
     * @return array
     */
    protected function getMinimalPrices(array $prices): array
    {
        $minPrices = [];
        foreach ($prices as $productId => $productPrices) {
            if (empty($minPrices[$productId])) {
                $minPrices[$productId] = [];
            }
            /** @var ProductPriceDTO $productPrice */
            foreach ($productPrices as $productPrice) {
                $unit = $productPrice->getUnit()->getCode();
                $currency = $productPrice->getPrice()->getCurrency();
                if (!isset($minPrices[$productId][$currency][$unit])
                    || $productPrice->getPrice()->getValue() < $minPrices[$productId][$currency][$unit]
                ) {
                    $minPrices[$productId][$currency][$unit] = $productPrice->getPrice()->getValue();
                }
            }
        }

        return $minPrices;
    }
}
