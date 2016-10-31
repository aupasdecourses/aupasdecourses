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
        return new Response('ORDER INDEX CONTROLLER');
    }

    public function ordersOneAction(Request $request)
    {
        return new Response('ORDER ONE CONTROLLER');
    }

   public function ordersAllAction(Request $request)
    {
        return new Response('ORDER ALL CONTROLLER');
    }
}
