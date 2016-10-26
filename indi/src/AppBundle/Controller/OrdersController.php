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
//        return $this->render('default/index.html.twig', [
//              'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
//              ]);
    }

    public function ordersOneAction(Request $request)
    {
        return new Response('ORDER ONE');
//        return $this->render('default/index.html.twig', [
//              'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
//              ]);
    }

   public function ordersAllAction(Request $request)
    {
        return new Response('ORDER ALL');
//        return $this->render('default/index.html.twig', [
//              'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
//              ]);
    }
}
