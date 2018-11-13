<?php

namespace Demo\Bundle\PricingBundle\Controller\Frontend;

use Oro\Bundle\ProductBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Get prices by products.
 */
class AjaxProductPriceController extends Controller
{
    /**
     * @Route("/get-prices-for-products", name="demo_pricing_frontend_prices_by_products", options={"expose"=true})
     * @Method({"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getPricesByProductsAction(Request $request): JsonResponse
    {
        $products = $this->getProductsByRequest($request);
        $prices = [];
        if (count($products)) {
            $pricesProvider = $this->container->get('oro_pricing.layout.data_provider.frontend_product_prices');
            $prices = $pricesProvider->getByProducts($products);
        }

        return new JsonResponse($prices);
    }

    /**
     * @param Request $request
     * @return array|Product[]
     */
    protected function getProductsByRequest(Request $request)
    {
        $productIds = array_unique($request->get('product_ids', []));
        $products = $this->getDoctrine()->getManagerForClass(Product::class)
            ->getRepository(Product::class)
            ->findBy(['id' => $productIds]);

        return $products;
    }
}
