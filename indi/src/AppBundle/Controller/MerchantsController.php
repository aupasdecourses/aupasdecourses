<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class MerchantsController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */

    public function indexAction(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
    }


    public function merchantsOne(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
    }
    
    public function merchantsAll(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
    }


}

