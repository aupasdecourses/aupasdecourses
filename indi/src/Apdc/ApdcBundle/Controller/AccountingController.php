<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{

	public function customersOrdersAction(Request $request)
	{
		if(!$this->isGranted('ROLE_INDI_COMPTABILITE')) {
			return $this->redirectToRoute('root');
		}	

		$mage	= $this->container->get('apdc_apdc.magento');
		$bill	= $this->container->get('apdc_apdc.billing');

		$defaultDataCSV = array('message' => 'Export');
		$formCSV = $this->createFormBuilder($defaultDataCSV)
			->add("Exporter", SubmitType::class, array('label' => 'Exporter en CSV', 'attr' => array('class' => 'btn btn-lg btn-success', 'style' => 'float:right')))
			->getForm();
		$formCSV->handleRequest($request);
														
		
		if (isset($_GET['date_debut'])) {
			$date_debut = $_GET['date_debut'];
			$date_fin	= $_GET['date_fin'];
			$date_fin = date('Y-m-d', strtotime(str_replace('/', '-', $date_fin)));
			$orders		= $mage->getOrdersByCustomer($date_debut, $date_fin);

			if ($formCSV->isSubmitted() && $formCSV->isValid()) {
				$response = new StreamedResponse();
				$response->setCallback(function() use($orders) {
					$handle = fopen('php://output', 'w+');

					fputcsv($handle, array(
						'Cree le',
						'# Commande',
						'Nom client',
						'Total produits HT',
						'Total produits TVA',
						'Total produits TTC',
						'Frais livraison HT',
						'Frais livraison TVA',
						'Frais livraison TTC',
						'Discount',
						'Total commande TTC'
					),';');

					foreach ($orders as $order) {
						fputcsv($handle, array(
							date('d/m/Y H:i', strtotime($order['created_at'])),
							$order['numero_commande'],
							$order['nom_client'],
							$order['total_produits_HT'],
							$order['total_produits_TVA'],
							$order['total_produits_TTC'],
							$order['frais_livraison_HT'],
							$order['frais_livraison_TVA'],
							$order['frais_livraison_TTC'],
							$order['discount'],
							$order['total_commande_TTC']
						),';');
					}

					fclose($handle);
				});

				$response->setStatusCode(200);
				$response->headers->set('Content-Type', 'text/csv; charset=utf-8');
				$response->headers->set('Content-Disposition', 'attachment; filename="commandes-par-client-du"'.date('d/m/Y', strtotime($date_debut)).'"au"'.date('d/m/Y', strtotime($date_fin)).'".csv"');

				return $response;
			}
		}

		return $this->render('ApdcApdcBundle::accounting/customers_orders.html.twig', [
			'orders'		=> $orders,
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
			'formCSV'		=> $formCSV->createView(),
		]);
	}
}
