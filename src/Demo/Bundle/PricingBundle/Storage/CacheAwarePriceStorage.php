<?php

namespace Demo\Bundle\PricingBundle\Storage;

use Demo\Bundle\PricingBundle\Cache\PriceCache;
use Oro\Bundle\PricingBundle\Model\DTO\ProductPriceDTO;
use Oro\Bundle\PricingBundle\Model\ProductPriceScopeCriteriaInterface;
use Oro\Bundle\PricingBundle\Storage\ProductPriceStorageInterface;
use Oro\Bundle\ProductBundle\Entity\Product;

/**
 * Load prices from external storage unless found in local cache.
 */
class CacheAwarePriceStorage implements ProductPriceStorageInterface
{
    /**
     * @var ProductPriceStorageInterface
     */
    private $remoteStorage;

    /**
     * @var ProductPriceStorageInterface
     */
    private $localStorage;

    /**
     * @var PriceCache
     */
    private $cache;

    /**
     * @param ProductPriceStorageInterface $priceStorage
     * @param ProductPriceStorageInterface $cacheStorage
     * @param PriceCache $cache
     */
    public function __construct(
        ProductPriceStorageInterface $priceStorage,
        ProductPriceStorageInterface $cacheStorage,
        PriceCache $cache
    ) {
        $this->remoteStorage = $priceStorage;
        $this->localStorage = $cacheStorage;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrices(
        ProductPriceScopeCriteriaInterface $scopeCriteria,
        array $products,
        array $productUnitCodes = null,
        array $currencies = null
    ) {
        $localPrices = $this->localStorage->getPrices($scopeCriteria, $products, $productUnitCodes, $currencies);

        $productsToLoad = $this->getProductsToLoad($products, $localPrices);
        $remotePrices = [];
        if ($productsToLoad) {
            $remotePrices = $this->remoteStorage->getPrices(
                $scopeCriteria,
                $productsToLoad,
                $productUnitCodes,
                $currencies
            );
            $this->cache->save($scopeCriteria, $remotePrices);
        }

        return array_merge($localPrices, $remotePrices);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCurrencies(ProductPriceScopeCriteriaInterface $scopeCriteria)
    {
        return $this->remoteStorage->getSupportedCurrencies($scopeCriteria);
    }

    /**
     * @param array|Product[] $products
     * @param array|ProductPriceDTO[] $localPrices
     * @return array|Product[]
     */
    protected function getProductsToLoad(array $products, array $localPrices): array
    {
        if (!$localPrices) {
            return $products;
        }

        $localProductIds = [];
        foreach ($localPrices as $localPrice) {
            $localProductIds[$localPrice->getProduct()->getId()] = true;
        }

        return array_filter($products, function (Product $product) use ($localProductIds) {
            return !array_key_exists($product->getId(), $localProductIds);
        });
    }
}
