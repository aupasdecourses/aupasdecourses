<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ToolController extends Controller
{
	public function productAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		return $this->render('ApdcApdcBundle::tool/product.html.twig');
	}

	public function merchantAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}
		$mage = $this->container->get('apdc_apdc.magento');

		return $this->render('ApdcApdcBundle::tool/merchant.html.twig');
	}

	public function categoryAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}
		$mage = $this->container->get('apdc_apdc.magento');

		return $this->render('ApdcApdcBundle::tool/category.html.twig');
	}

	public function contactLPRAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}
		$mage = $this->container->get('apdc_apdc.magento');

		return $this->render('ApdcApdcBundle::tool/lpr_info.html.twig');
	}

	public function commentsHistoryAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');

		$comments = $stats->getCommentsHistory();
		
		return $this->render('ApdcApdcBundle::tool/comments_history.html.twig');	
	}
}
