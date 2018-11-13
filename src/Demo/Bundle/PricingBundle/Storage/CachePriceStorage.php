<?php

namespace Demo\Bundle\PricingBundle\Storage;

use Demo\Bundle\PricingBundle\Cache\PriceCache;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\PricingBundle\Model\DTO\ProductPriceDTO;
use Oro\Bundle\PricingBundle\Model\ProductPriceScopeCriteriaInterface;
use Oro\Bundle\PricingBundle\Storage\ProductPriceStorageInterface;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ProductBundle\Entity\ProductUnit;

/**
 * Cache based prices storage.
 */
class CachePriceStorage implements ProductPriceStorageInterface
{
    /**
     * @var PriceCache
     */
    private $cache;

    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @param PriceCache $cache
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(PriceCache $cache, DoctrineHelper $doctrineHelper)
    {
        $this->cache = $cache;
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCurrencies(ProductPriceScopeCriteriaInterface $scopeCriteria)
    {
        /**
         * Only test solution, must be rewrote for the real case
         */
        return ['USD'];
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
        $pricesByProduct = $this->cache->load($scopeCriteria, $products);

        return $this->getPricesByData($pricesByProduct);
    }

    /**
     * @param array $pricesByProduct
     * @return array
     */
    protected function getPricesByData(array $pricesByProduct): array
    {
        $result = [];
        if (!empty($pricesByProduct)) {
            foreach ($pricesByProduct as $productPricesData) {
                foreach ($productPricesData as $priceData) {
                    $result[] = new ProductPriceDTO(
                        $this->getProduct($priceData),
                        Price::create($priceData[ProductPriceDTO::PRICE], $priceData[ProductPriceDTO::CURRENCY]),
                        $priceData[ProductPriceDTO::QUANTITY],
                        $this->getProductUnit($priceData)
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @param array $priceData
     * @return Product|object
     */
    protected function getProduct($priceData): Product
    {
        return $this->doctrineHelper->getEntityReference(Product::class, $priceData[ProductPriceDTO::PRODUCT]);
    }

    /**
     * @param array $priceData
     * @return ProductUnit|object
     */
    protected function getProductUnit($priceData): ProductUnit
    {
        return $this->doctrineHelper->getEntityReference(ProductUnit::class, $priceData[ProductPriceDTO::UNIT]);
    }
}
