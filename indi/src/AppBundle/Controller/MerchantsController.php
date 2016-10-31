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
        return new Response('MERCHANT INDEX CONTROLER');
    }

    public function merchantsOneAction(Request $request)
    {
        return new Response('MERCHANT ONE CONTROLER');
    }
    
    public function merchantsAllAction(Request $request)
    {
        return new Response('MERCHANT ALL CONTROLER');
    }
}

