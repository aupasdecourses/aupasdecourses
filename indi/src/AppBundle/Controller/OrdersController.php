<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function ordersOne(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
//        return $this->render('default/index.html.twig', [
//              'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
//              ]);
    }


   public function ordersAll(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
//        return $this->render('default/index.html.twig', [
//              'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
//              ]);
    }

  public function indexAction(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
//        return $this->render('default/index.html.twig', [
//              'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
//              ]);
    }



}

