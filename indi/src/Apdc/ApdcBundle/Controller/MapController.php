<?php

namespace Apdc\ApdcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MapController extends Controller
{
    public function merchantsAction(Request $request)
    {
		   
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}

		$session = $request->getSession();

        $stats = $this->container->get('apdc_apdc.stats');
        $mage = $this->container->get('apdc_apdc.magento');

        $comparaisonMerchants = $stats->compareMerchants();

        $json_data_for_merchants = $stats->getMerchantsStatData();

        $entity_submit_new_merchants = new \Apdc\ApdcBundle\Entity\Model();
        $form_new_merchants = $this->createFormBuilder($entity_submit_new_merchants);
        $form_new_merchants = $form_new_merchants->getForm();

        if ($request->isMethod('POST')) {
            $form_new_merchants->handleRequest($request);
            $new_merchants_to_add = $stats->getNewShopData();

            try {
                foreach ($new_merchants_to_add as $content) {

							$mage->updateEntryToGeocode(
								['id_shop'			=> $content['id_shop']],
								['address'			=> $content['address'],
								'postcode'			=> $content['postcode'],
								'city'				=> $content['city'],
								'lat'				=> $content['lat'],
								'long'				=> $content['long'],
								'former_address'	=> $content['former_address'],
								'whoami'			=> 'SHOP',
							]);
						}

            $session->getFlashBag()->add('success', 'MAJ commercants sur la carte effectuée');
            return $this->redirectToRoute('mapMerchants');

            } catch (Exception $e) {
                $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de la MAJ des commercants sur la carte');
            }
        }

        $google_key = \Mage::getStoreConfig('api_key/google/google_nav');

        return $this->render('ApdcApdcBundle::map/merchants.html.twig',
            [
                'json_data_for_shops' => $json_data_for_merchants,
                'form_new_merchants' => $form_new_merchants->createView(),
                'comparaisonMerchants' => $comparaisonMerchants,
                'google_key' => $google_key,
                'url_google_maps' => 'https://maps.googleapis.com/maps/api/js?key='.$google_key.'&v=3&sensor=false',
            ]);
    }

    public function customersAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
            return $this->redirectToRoute('root');
        }

        $session = $request->getSession();

        $stats = $this->container->get('apdc_apdc.stats');
        $mage = $this->container->get('apdc_apdc.magento');

        $comparaisonCustomers = $stats->compareCustomers();

        $json_data_for_customers = $stats->getCustomerMapData();

        $entity_submit_new_customers = new \Apdc\ApdcBundle\Entity\Model();
        $form_new_customers = $this->createFormBuilder($entity_submit_new_customers);
        $form_new_customers = $form_new_customers->getForm();

        if ($request->isMethod('POST')) {
            $form_new_customers->handleRequest($request);
            $new_customers_to_add = $stats->addLatAndLong();

            try {
                foreach ($new_customers_to_add as $content) {

							$mage->updateEntryToGeocode(
								['id_customer' => $content['id_customer']],
								['address' => $content['address'],
								'postcode' => $content['postcode'],
								'city' => $content['city'],
								'lat' => $content['lat'],
								'long' => $content['long'],
								'former_address' => $content['former_address'],
								'whoami' => 'CUSTOMER',
							]);
						}
                $session->getFlashBag()->add('success', 'MAJ clients sur la carte effectuée');
				return $this->redirectToRoute('mapCustomers');

            } catch (Exception $e) {
                $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de la MAJ des clients sur la carte');
            }
        }

        $google_key = \Mage::getStoreConfig('api_key/google/google_nav');

        return $this->render('ApdcApdcBundle::map/customers.html.twig',
            [
                'json_data_for_customers' => $json_data_for_customers,
                'form_new_customers' => $form_new_customers->createView(),
                'comparaisonCustomers' => $comparaisonCustomers,
                'google_key' => $google_key,
                'url_google_maps' => 'https://maps.googleapis.com/maps/api/js?key='.$google_key.'&v=3&sensor=false',
        ]);
    }
}
