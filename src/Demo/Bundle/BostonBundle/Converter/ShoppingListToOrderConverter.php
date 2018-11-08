<?php

namespace Demo\Bundle\BostonBundle\Converter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerUserAddress;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Entity\OrderAddress;
use Oro\Bundle\OrderBundle\Entity\OrderLineItem;
use Oro\Bundle\OrderBundle\Handler\OrderCurrencyHandler;
use Oro\Bundle\OrderBundle\Handler\OrderTotalsHandler;
use Oro\Bundle\PricingBundle\Entity\PriceTypeAwareInterface;
use Oro\Bundle\PricingBundle\Provider\FrontendProductPricesDataProvider;
use Oro\Bundle\ShoppingListBundle\Entity\Repository\ShoppingListRepository;
use Oro\Bundle\ShoppingListBundle\Entity\ShoppingList;
use Oro\Bundle\ShoppingListBundle\Entity\LineItem;

class ShoppingListToOrderConverter
{
    const FIELD_OFFER = 'offer';
    const FIELD_QUANTITY = 'quantity';

    /** @var OrderCurrencyHandler */
    protected $orderCurrencyHandler;

    /** @var ManagerRegistry */
    protected $registry;

    /** @var OrderTotalsHandler */
    protected $orderTotalsHandler;

    /** @var FrontendProductPricesDataProvider */
    protected $productPriceProvider;

    /** @var DoctrineHelper */
    private $doctrineHelper;

    /**
     * @param OrderCurrencyHandler $orderCurrencyHandler
     * @param OrderTotalsHandler   $orderTotalsHandler
     * @param ManagerRegistry      $registry
     * @param FrontendProductPricesDataProvider $productPriceProvider
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(
        OrderCurrencyHandler $orderCurrencyHandler,
        OrderTotalsHandler $orderTotalsHandler,
        ManagerRegistry $registry,
        FrontendProductPricesDataProvider $productPriceProvider,
        DoctrineHelper $doctrineHelper
    ) {
        $this->orderCurrencyHandler = $orderCurrencyHandler;
        $this->orderTotalsHandler = $orderTotalsHandler;
        $this->registry = $registry;
        $this->productPriceProvider = $productPriceProvider;
        $this->doctrineHelper = $doctrineHelper;
    }

    public function convert($id)
    {
        /** @var ShoppingListRepository $productRepository */
        $repository = $this->doctrineHelper->getEntityRepositoryForClass(ShoppingList::class);

        /** @var ShoppingList $list */
        $list = $repository->find($id);

        $customerUser = $list->getCustomerUser();
        $shippingAddress = $this->createOrderAddress($customerUser->getPrimaryAddress());
        $billingAddress = $this->createOrderAddress($customerUser->getPrimaryAddress());

        $order = new Order();
        $order
            ->setCustomer($list->getCustomer())
            ->setCustomerUser($customerUser)
            ->setOwner($list->getOwner())
            ->setOrganization($list->getOrganization())
            ->setShippingAddress($shippingAddress)
            ->setBillingAddress($billingAddress)
            ->setSourceEntityClass(ClassUtils::getClass($list))
            ->setSourceEntityId($list->getId())
            ->setSourceEntityIdentifier($list->getIdentifier())
            ->setCurrency('USD')
            ;

        if ($order->getCurrency() === null) {
            $this->orderCurrencyHandler->setOrderCurrency($order);
        }

        $lineItems = $list->getLineItems();
        $prices = $this->productPriceProvider->getProductsMatchedPrice($lineItems->toArray());


        foreach ($lineItems as $lineItem) {
            /** @var LineItem $lineItem */
            $product = $lineItem->getProduct();
            $unit = $lineItem->getUnit();

            $orderLineItem = new OrderLineItem();
            $orderLineItem
                ->setFreeFormProduct($product->getDefaultName())
                ->setProductSku($product->getSku())
                ->setProduct($product)
                ->setProductUnit($unit)
                ->setQuantity($lineItem->getQuantity())
                ->setPriceType(PriceTypeAwareInterface::PRICE_TYPE_UNIT)
                ->setPrice($prices[$product->getId()][$unit->getCode()])
                ->setFromExternalSource(true)
            ;

            $order->addLineItem($orderLineItem);
        }

        $this->orderTotalsHandler->fillSubtotals($order);

        $manager = $this->registry->getManagerForClass('OroOrderBundle:Order');
        $manager->persist($order);
        $manager->flush();

        return $order;
    }


    /**
     * @param CustomerUserAddress $customerUserAddress
     * @param Customer $customer
     *
     * @return null|OrderAddress
     */
    protected function createOrderAddress(CustomerUserAddress $customerUserAddress)
    {
        $orderAddress = null;

        if ($customerUserAddress) {
            $orderAddress = new OrderAddress();

            $orderAddress->setCustomerUserAddress($customerUserAddress);
            $orderAddress->setLabel($customerUserAddress->getLabel());
            $orderAddress->setStreet($customerUserAddress->getStreet());
            $orderAddress->setStreet2($customerUserAddress->getStreet2());
            $orderAddress->setCity($customerUserAddress->getCity());
            $orderAddress->setPostalCode($customerUserAddress->getPostalCode());
            $orderAddress->setOrganization($customerUserAddress->getOrganization());
            $orderAddress->setRegionText($customerUserAddress->getRegionText());
            $orderAddress->setNamePrefix($customerUserAddress->getNamePrefix());
            $orderAddress->setFirstName($customerUserAddress->getFirstName());
            $orderAddress->setMiddleName($customerUserAddress->getMiddleName());
            $orderAddress->setLastName($customerUserAddress->getLastName());
            $orderAddress->setNameSuffix($customerUserAddress->getNameSuffix());
            $orderAddress->setRegion($customerUserAddress->getRegion());
            $orderAddress->setCountry($customerUserAddress->getCountry());
            $orderAddress->setPhone($customerUserAddress->getPhone());
            $orderAddress->setFromExternalSource(true);
        }

        return $orderAddress;
    }


    /**
     * @param Price $shippingEstimate
     * @param Order $order
     */
    protected function fillShippingCost(Price $shippingEstimate, Order $order)
    {
        $estimatedShippingCostAmount = $shippingEstimate->getValue();
        $shippingEstimateCurrency = $shippingEstimate->getCurrency();
        $orderCurrency = $order->getCurrency();
        if ($orderCurrency !== $shippingEstimateCurrency) {
            $estimatedShippingCostAmount *= $this->getExchangeRate($shippingEstimateCurrency, $orderCurrency);
        }

        $order->setEstimatedShippingCostAmount($estimatedShippingCostAmount);
    }

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    protected function getExchangeRate($fromCurrency, $toCurrency)
    {
        return 1.0;
    }
}
