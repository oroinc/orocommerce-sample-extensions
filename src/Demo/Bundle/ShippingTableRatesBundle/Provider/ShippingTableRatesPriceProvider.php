<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Provider;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Demo\Bundle\ShippingTableRatesBundle\Entity\ShippingTableRate;

class ShippingTableRatesPriceProvider implements ShippingTableRatesPriceProviderInterface
{
    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var float */
    private $handlingFee = 0;

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
    public function getPrice(ShippingContextInterface $context)
    {
        $shippingAddress = $context->getShippingAddress();

        $repository = $this->doctrineHelper->getEntityRepository(ShippingTableRate::class);
        $qb = $repository->createQueryBuilder('strm');
        $qb->select([
            'strm.price',
            'strm.handlingFee',
            'CASE WHEN strm.region IS NULL THEN 0 ELSE 1 END as HIDDEN hasRegion',
            'CASE WHEN strm.postalCode IS NULL THEN 0 ELSE 1 END as HIDDEN hasPostalCode',
        ]);

        $expr = $qb->expr();
        $qb->where($expr->andX(
            $expr->eq('strm.currency', ':currency'),
            $expr->eq('strm.country', ':country'),
            $expr->orX($expr->eq('strm.region', ':region'), $expr->isNull('strm.region')),
            $expr->orX($expr->eq('strm.postalCode', ':postalCode'), $expr->isNull('strm.postalCode'))
        ));

        $qb->setParameters([
            'currency' => $context->getCurrency(),
            'country' => $shippingAddress->getCountryIso2(),
            'region' => sprintf('%s-%s', $shippingAddress->getCountryIso2(), $shippingAddress->getRegionCode()),
            'postalCode' => $shippingAddress->getPostalCode(),
        ]);

        $qb->setMaxResults(1);
        $qb->orderBy('hasRegion', 'DESC');
        $qb->addOrderBy('hasPostalCode', 'DESC');
        $qb->addOrderBy('strm.id');

        $result = $qb->getQuery()->getOneOrNullResult();
        if ($result) {
            $this->handlingFee = (float)$result['handlingFee'];

            return (float)$result['price'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlingFee(ShippingContextInterface $context)
    {
        return $this->handlingFee;
    }
}
