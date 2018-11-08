<?php

namespace Demo\Bundle\BostonBundle\Controller;

use Demo\Bundle\BostonBundle\Converter\ShoppingListToOrderConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CheckoutController extends Controller
{
    /**
     * @Route("/bcheckout", name="demo_boston_checkout")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function createAction(Request $request)
    {
        /** @var ShoppingListToOrderConverter $converter */
        $converter = $this->get('demo_boston.service.shopping_list_to_order_converter');
        $order = $converter->convert($request->get('id'));
        return $this->redirectToRoute('oro_order_frontend_view', ['id' => $order->getId()]);
    }
}
