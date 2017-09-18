<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{
		return $this->render('ApdcApdcBundle::home/index.html.twig');
	}

	public function downloadAction(Request $request,$filename)
	{

	    $path = $this->get('kernel')->getRootDir(). "/../web/docs/billing/";
	    $content = file_get_contents($path.$filename);

	    $response = new Response();

	    //set headers
	    $response->headers->set('Content-Type', 'mime/type');
	    $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);

	    $response->setContent($content);
	    return $response;
	}
}
