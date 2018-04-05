<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
		
		return $this->render('ApdcApdcBundle::tool/comments/history.html.twig', [
			'comments'	=> $comments,
		]);	
	}

	public function commentsFormAction(Request $request, $order_id = null, $merchants_comment_choice = null)
	{
		if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}

		$entity_comment = new \Apdc\ApdcBundle\Entity\Comment();
		$form_comment = $this->createForm(\Apdc\ApdcBundle\Form\Comment::class, $entity_comment);
		
		// Override merchant_id choicetype
		if (!is_null($merchants_comment_choice)) {
			$form_comment->add('merchant_id', ChoiceType::class, [
				'label' => 'Commercant',
				'attr'	=> [
					'class'	=> 'form-control'
				],
				'choices' => $merchants_comment_choice,
			]);
		}

		$form_comment->handleRequest($request);

		return $this->render('ApdcApdcBundle::tool/comments/form.html.twig', [
			'form_comment'				=> $form_comment->createView(),
			'order_id'					=> $order_id,
		]);		
	}

	public function commentsProcessAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');
		$form = $request->query->get('comment');
		$session = $request->getSession();

		$order_id = $form['order_id'];

		$stats->addEntryToCommentHistory([
			'created_at' 	=> date('Y-m-d H:i:s'),
			'author'		=> $this->getUser()->getUsername(),
			'comment_type'	=> $form['type'],
			'comment_text'	=> $form['text'],
			'order_id'		=> $order_id,
			'merchant_id'	=> $form['merchant_id'],
		]);

		$session->getFlashBag()->add('success', 'Commentaire bien crée pour la commande ' . $order_id);
		return $this->redirect($request->headers->get('referer'));
	}
}
