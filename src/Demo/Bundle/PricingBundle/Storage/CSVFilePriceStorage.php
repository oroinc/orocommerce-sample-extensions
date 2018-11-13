<?php

namespace Demo\Bundle\PricingBundle\Storage;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\PricingBundle\Model\DTO\ProductPriceDTO;
use Oro\Bundle\PricingBundle\Model\ProductPriceScopeCriteriaInterface;
use Oro\Bundle\PricingBundle\Storage\ProductPriceStorageInterface;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ProductBundle\Entity\ProductUnit;

/**
 * CSV based prices storage.
 */
class CSVFilePriceStorage implements ProductPriceStorageInterface
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
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
        $plFile = $this->locatePriceListFile($scopeCriteria);

        return $this->readPriceList($products, $plFile);
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
     * @param ProductPriceScopeCriteriaInterface $scopeCriteria
     * @return string
     */
    protected function locatePriceListFile(ProductPriceScopeCriteriaInterface $scopeCriteria): string
    {
        $plFile = 'global.csv';

        if ($scopeCriteria->getWebsite()) {
            $websitePriceList = sprintf('website_%d.csv', $scopeCriteria->getWebsite()->getId());
            if (is_readable(__DIR__ . '/price_lists/' . $websitePriceList)) {
                $plFile = $websitePriceList;
            }
        }

        if ($scopeCriteria->getCustomer() && $scopeCriteria->getWebsite()) {
            $customerPricesList = sprintf(
                'customer_%d_%d.csv',
                $scopeCriteria->getWebsite()->getId(),
                $scopeCriteria->getCustomer()->getId()
            );
            if (is_readable(__DIR__ . '/price_lists/' . $customerPricesList)) {
                $plFile = $customerPricesList;
            }
        }

        return $plFile;
    }

    /**
     * @param array|Product[] $products
     * @param string $plFile
     * @return array
     */
    protected function readPriceList(array $products, $plFile): array
    {
        // Emulate slow API connection
        //sleep(5);

        $productsBySku = [];
        foreach ($products as $product) {
            $productsBySku[$product->getSku()] = $product;
        }

        $pl = fopen(__DIR__ . '/price_lists/' . $plFile, 'r');
        $prices = [];
        $headers = fgetcsv($pl, 1000, ',');

        while (($data = fgetcsv($pl, 1000, ',')) !== false) {
            $data = array_combine($headers, array_values($data));
            if (array_key_exists($data['sku'], $productsBySku)) {
                $prices[] = $this->createPriceDTO($data, $productsBySku[$data['sku']]);
            }
        }

        return $prices;
    }

    /**
     * @param array $data
     * @param Product $product
     * @return ProductPriceDTO
     */
    private function createPriceDTO(array $data, Product $product): ProductPriceDTO
    {
        /** @var ProductUnit $productUnit */
        $productUnit = $this->doctrineHelper->getEntityReference(ProductUnit::class, $data['unit']);

        return new ProductPriceDTO(
            $product,
            Price::create($data['price'], $data['currency']),
            $data['quantity'],
            $productUnit
        );
    }
}
