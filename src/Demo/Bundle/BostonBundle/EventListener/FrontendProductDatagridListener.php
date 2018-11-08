<?php

namespace Demo\Bundle\BostonBundle\EventListener;

use Oro\Bundle\DataGridBundle\Datasource\ResultRecord;
use Oro\Bundle\DataGridBundle\Event\PreBuild;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyInterface;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Oro\Bundle\SearchBundle\Datagrid\Event\SearchResultAfter;

class FrontendProductDatagridListener
{

    /** @var DoctrineHelper */
    private $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public function onPreBuild(PreBuild $event)
    {
        $config = $event->getConfig();

        $config->offsetAddToArrayByPath(
            '[properties]',
            [
                'izod' => [
                    'type'          => 'field',
                    'frontend_type' => PropertyInterface::TYPE_STRING,
                ],
                'melt_flow' => [
                    'type'          => 'field',
                    'frontend_type' => PropertyInterface::TYPE_STRING,
                ],
                'flex_modulus' => [
                    'type'          => 'field',
                    'frontend_type' => PropertyInterface::TYPE_STRING,
                ],
                'tensile_strength' => [
                    'type'          => 'field',
                    'frontend_type' => PropertyInterface::TYPE_STRING,
                ],
            ]
        );
    }

    public function onResultAfter(SearchResultAfter $event)
    {
        /** @var ResultRecord[] $records */
        $records = $event->getRecords();

        /** @var ProductRepository $productRepository */
        $productRepository = $this->doctrineHelper->getEntityRepositoryForClass(Product::class);

        foreach ($records as $record) {
            $product = $productRepository->find($record->getValue('id'));
            if ($product) {
                $record->addData(['izod' => $product->getIzod() ? $product->getIzod() : 'NB-NB']);
                $record->addData(['melt_flow' => $product->getMeltFlow() ? $product->getMeltFlow() : 'N/A']);
                $record->addData(['flex_modulus' => $product->getFlexModulus() ? $product->getFlexModulus() : 'N/A']);
                $record->addData(['tensile_strength' => $product->getTensileStrength() ? $product->getTensileStrength() : 'N/A']);
            } else {
                $record->addData(['izod' => 'N/A']);
                $record->addData(['melt_flow' => 'N/A']);
                $record->addData(['flex_modulus' => 'N/A']);
                $record->addData(['tensile_strength' => 'N/A']);
            }
        }
    }
}
