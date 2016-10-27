<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class ShippingController extends Controller
{
    public function indexAction(Request $request)
    {
        return new Response('Shipping CONTROLER');
    }

    public function shippingAllAction(Request $request)
    {
        return new Response('shipping all CONTROLER');
    }
}

