<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class MerchantsController extends Controller
{
    public function indexAction(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
    }

    public function merchantsOneAction(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
    }
    
    public function merchantsAllAction(Request $request)
    {
        return new Response('DEFAULT CONTROLER');
    }
}

