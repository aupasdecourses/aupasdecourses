<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
  public function indexAction(Request $request)
    {
        return new Response('ORDER INDEX');
    }

    public function ordersOneAction(Request $request)
    {
        return new Response('ORDER ONE');
    }

   public function ordersAllAction(Request $request)
    {
        return new Response('ORDER ALL');
    }
}
