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

		if (isset($_GET['date_debut'])) {
			$date_debut = $_GET['date_debut'];
			$date_fin	= $_GET['date_fin'];	
			$comments = $stats->getCommentsHistory(0, 9999999999, $date_debut, $date_fin);
		}

		return $this->render('ApdcApdcBundle::tool/comments/history.html.twig', [
			'comments'		=> $comments,
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
		]);	
	}

	public function commentsFormAction(Request $request, $order_id = null, $merchants_comment_choice = null)
	{
		if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');

		$entity_comment = new \Apdc\ApdcBundle\Entity\Comment();
		$form_comment = $this->createForm(\Apdc\ApdcBundle\Form\Comment::class, $entity_comment);

		// Override default merchant_id choicetype
		if (!is_null($merchants_comment_choice)) {
			$form_comment->add('merchant_id', ChoiceType::class, [
				'required'	=> true,
				'label' 	=> 'Commercant',
				'attr'		=> [
					'class'		=> 'form-control'
				],
				'choices' 	=> $merchants_comment_choice,
			]);
		}

		// Override default type choicetype
		if (is_null($order_id)) {
			$types_comment_choice = [];
			foreach ($stats->getCommentsType() as $t) {
				$types_comment_choice[$t['label']] = $t['type'];
			}
			unset($types_comment_choice['Commentaire visible par le client']);

			$types_comment_choice = array_merge(['Selectionner un type' => ''], $types_comment_choice);

			$form_comment->add('type', ChoiceType::class, [
				'required'	=> true,
				'label'		=> 'Type de commentaire',
				'attr'		=> [
					'class'		=> 'form-control'
				],
				'choices'	=> $types_comment_choice,
				'group_by'	=> function($key, $value, $index) {
					if (strpos($key, "not_visible") !== false) {
						return 'Commentaires internes';
					}
					if (strpos($key, "is_visible") !== false) {
						return 'Commentaires visibles';
					}
				},
			]);
		}

		$already_visible_customer_comment = 0;
		if (!is_null($order_id)) {
			$commentsHistory = $stats->getCommentsHistory($order_id, $order_id);
        	foreach ($commentsHistory as $history) {
            	if (strpos($history['comment_type'], "customer_is_visible") !== false) {
                	$already_visible_customer_comment = 1;
            	}
        	}
		}

		$form_comment->handleRequest($request);

		return $this->render('ApdcApdcBundle::tool/comments/form.html.twig', [
			'form_comment'						=> $form_comment->createView(),
			'order_id'							=> $order_id,
			'already_visible_customer_comment'	=> $already_visible_customer_comment,
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
			'created_at' 			=> date('Y-m-d H:i:s'),
			'author'				=> $this->getUser()->getUsername(),
			'comment_type'			=> $form['type'],
			'comment_text'			=> $form['text'],
			'order_id'				=> $order_id,
			'merchant_id'			=> $form['merchant_id'],
			'associated_order_id'	=> $form['associated_order_id'],
		]);

		$session->getFlashBag()->add('success', 'Commentaire bien crée pour la commande ' . $order_id);
		return $this->redirect($request->headers->get('referer'));
	}
}
