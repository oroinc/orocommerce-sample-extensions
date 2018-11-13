<?php

namespace Demo\Bundle\PricingBundle\Cache;

use Doctrine\Common\Cache\Cache;
use Oro\Bundle\PricingBundle\Model\DTO\ProductPriceDTO;
use Oro\Bundle\PricingBundle\Model\ProductPriceScopeCriteriaInterface;
use Oro\Bundle\ProductBundle\Entity\Product;

/**
 * Price cache implementation.
 */
class PriceCache
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param ProductPriceScopeCriteriaInterface $scopeCriteria
     * @param array|ProductPriceDTO[] $externalPrices
     */
    public function save(
        ProductPriceScopeCriteriaInterface $scopeCriteria,
        array $externalPrices
    ) {
        $pricesByProduct = [];
        foreach ($externalPrices as $externalPrice) {
            $pricesByProduct[$externalPrice->getProduct()->getId()][] = $externalPrice->toArray();
        }

        $cachePrefix = $this->getCachePrefix($scopeCriteria);
        foreach ($pricesByProduct as $productId => $prices) {
            $this->cache->save($cachePrefix . '_' . $productId, $prices);
        }
    }

    /**
     * @param ProductPriceScopeCriteriaInterface $scopeCriteria
     * @param array|Product[] $products
     * @return array
     */
    public function load(ProductPriceScopeCriteriaInterface $scopeCriteria, array $products): array
    {
        $pricesByProduct = [];
        $cacheKeyPrefix = $this->getCachePrefix($scopeCriteria);
        foreach ($products as $product) {
            $productId = $product->getId();
            $cacheKey = $cacheKeyPrefix . '_' . $productId;
            if ($this->cache->contains($cacheKey)) {
                $pricesByProduct[$productId] = $this->cache->fetch($cacheKey);
            }
        }

        return $pricesByProduct;
    }

    /**
     * @param ProductPriceScopeCriteriaInterface $scopeCriteria
     * @return string
     */
    protected function getCachePrefix(ProductPriceScopeCriteriaInterface $scopeCriteria): string
    {
        return ($scopeCriteria->getWebsite() ? (int)$scopeCriteria->getWebsite()->getId() : 0)
            . '_'
            . ($scopeCriteria->getCustomer() ? (int)$scopeCriteria->getCustomer()->getId() : 0);
    }
}
