<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class PickingController extends Controller
{

    public function indexAction(Request $request)
    {
        return new Response('Inedx Action');
    }

    public function pickingAll(Request $request)
    {
        return new Response('Picking controller');
    }

}

