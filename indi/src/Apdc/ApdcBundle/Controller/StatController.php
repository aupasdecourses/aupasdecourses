<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatController extends Controller
{

	public function noteAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stat');

		if(isset($_GET['date_debut'])) {
			$date_debut = $_GET['date_debut'];
			$date_fin = $stats->end_month($date_debut);
		}

		$notes	= $stats->getNotes($date_debut, $date_fin);

		return $this->render('ApdcApdcBundle::stat/note.html.twig', [
			'notes'			=> $notes,
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
		]);

	}

}
