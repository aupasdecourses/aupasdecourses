<?php

namespace Apdc\ApdcBundle\Services;

include '../../app/Mage.php';

define('FLOAT_NUMBER', 2);

class Stats
{

	public function __construct()
	{
		\Mage::app();
	}


	private function array_columns($array, $column_name)
	{
		return array_map(
			function ($element) use ($column_name) {
				return $element[$column_name];
			},
			$array
		);
	}

	/** get data from customer_entity + join sales_flat_order_address + join geocode_customers
	* fonction mère */
	public function getCustomerStatData()
	{
		$data = [];
		$orders = \Mage::getModel('sales/order')->getCollection()//->setOrder('entity_id', 'DESC')
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToFilter('status', array('in' => array(\Mage_Sales_Model_Order::STATE_COMPLETE, \Mage_Sales_Model_Order::STATE_CLOSED)));

		$orders->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id', array('postcode', 'street', 'city', 'telephone'));
		$orders->getSelect()->joinLeft('customer_entity', 'sales_flat_order_address.customer_id = customer_entity.entity_id', array('customer_created_at'=> 'customer_entity.created_at'));
		$orders->getSelect()->joinLeft('geocode_customers' ,'sales_flat_order_address.street = geocode_customers.former_address', array('address', 'lat', 'long'));

		$orders->addAttributeToFilter('address_type', 'shipping');
		$orders->getSelect()->columns('COUNT(*) AS nb_order')
			->columns('SUM(base_grand_total) AS amount_total, AVG(base_grand_total) AS average_orders, STDDEV(base_grand_total) AS std_dev_orders, MAX(base_grand_total) AS max_orders')
			->columns('MAX(main_table.created_at) AS last_order')
			->group('customer_id');

//		dump($orders->getSelect()->__toString());	
		foreach ($orders as $order) {
			$total_order=round($order->getAmountTotal(), FLOAT_NUMBER, PHP_ROUND_HALF_UP);

			array_push($data, [
					'nom_client'		=> $order->getCustomerName(),
					'id_client'			=> $order->getCustomerId(),
					'nb_commande'		=> $order->getNbOrder(),
					//'Total'			=> $order->getAmountTotal(),
					'panier_moyen'		=> round($order->getAverageOrders(), FLOAT_NUMBER, PHP_ROUND_HALF_UP),
					'panier_max'		=> round($order->getMaxOrders(), FLOAT_NUMBER, PHP_ROUND_HALF_UP),
					'ecart_type'		=> round($order->getStdDevOrders(), FLOAT_NUMBER, PHP_ROUND_HALF_UP),
					'inscription'		=> \Mage::helper('core')->formatDate($order->getData('customer_created_at'), 'short', false),
					'derniere_commande'	=> date('d/m/Y', strtotime($order->getLastOrder())),
					'rue'				=> $order->getStreet()[0], 
					'addr'				=> $order->getAddress(), // from geocode_customers
					'code_postal'		=> $order->getPostcode(),
					'ville'				=> $order->getCity(),
					//'Créé dans'		=> $order->getCreatedIn(),
					'email'				=> $order->getCustomerEmail(),
					'telephone'			=> $order->getTelephone(),
					'lat'				=> $order->getData('lat'),
					'lon'				=> $order->getData('long'),
				]);
		}

		return $data;
	}

	/** getCustomerStatData 
	 *	AND ADD NEW CUSTOMERS
	 *	fonction fille pour les stats clients, avec l'ajout de new users
	 **/
	public function stats_clients()
	{
		$data = $this->getCustomerStatData();	

		//Add customer who never ordered
		$customers = \Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToSelect('*');
		foreach ($customers as $customer) {
			$key = array_search($customer->getEmail(), $this->array_columns($data, 'Mail client'));
			if ($key == false) {
				array_push($data, [
					'nom_client'		=> $customer->getFirstname().' '.$customer->getLastname(),
					'id_client'			=> $customer->getCustomerId(),
					'nb_commande'		=> 0,
					'panier_moyen'		=> 0,
					'panier_max'		=> 0,
					'ecart_type'		=> 0,
					'inscription'		=> \Mage::helper('core')->formatDate($customer->getCreatedAt(), 'short', false),
					'derniere_commande'	=> 'NA',
					'rue'				=> '',
					'addr'				=> '',
					'code_postal'		=> $customer->getCreatedIn(),
					'ville'				=> '',
					'email'				=> $customer->getEmail(),
					'telephone'			=> '',
				]);
			}
	 	}
	 
		return $data;
	}


	public function cleanAddrForMap()
	{
		$stats = $this->getCustomerStatData();

//		$streetTypes		= ['allee','allée','avenue','boulevard','bd','bvd','chaussee','chemin','cite','cité','clos','cour','impasse','passage','place','pont','quai','rue','ruelle','route','voie'];

//		foreach ($stats as &$stat) {
//			foreach ($streetTypes as $streets) {
//				if (strpos($stat['addr'], $streets) || strpos($stat['addr'], ucfirst($streets)) || strpos($stat['addr'], strtoupper($streets))) {
//					$stat['lalalala'] = 'lalalala';	
//				}
//			}	
//		}


		/**	On supprime les virgules et tout le contenu des adresses après les \n , les tirets, 'interphone' , 'code'
		 *	strtr sur tous les caracteres accentués
		 */

		$bad_chars = ['À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'];
		$good_chars = ['A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y'];

		foreach ($stats as &$stat) {
			if (strpos($stat['addr'], "\n")) {
				$cut_pos		= strpos($stat['addr'], "\n");	
				$stat['addr']	= substr($stat['addr'], 0, $cut_pos);
			}
			if (strpos($stat['addr'], "-")) {
				$cut_pos_two	= strpos($stat['addr'], "-");
				$stat['addr']	= substr($stat['addr'], 0, $cut_pos_two);
			}
			if (strpos($stat['addr'], "code")) {
				$cut_pos_three	= strpos($stat['addr'], "code");
				$stat['addr']	= substr($stat['addr'], 0, $cut_pos_three);
			}
			if (strpos($stat['addr'], "interphone")) {
				$cut_pos_four	= strpos($stat['addr'], "interphone");
				$stat['addr']	= substr($stat['addr'], 0, $cut_pos_four);
			}

			$stat['addr'] = str_replace(",", "", $stat['addr']);

			$stat['addr'] = strtr($stat['addr'], array_combine($bad_chars, $good_chars));
		}

		return $stats;
	}






	private function geocodeAdress($adress) {
		$data	= [];
		$adress = urlencode(htmlentities($adress));
		$query	= 'http://nominatim.openstreetmap.org/search?format=json&street='.$adress.'&city=Paris&country=France&countrycodes=fr';
		$string = file_get_contents($query);
		$json	= json_decode($string, true);

		return $json;
	}

	public function addLatLongAndJsonEncode()
	{
		$stats = $this->cleanAddrForMap();
	//		$stats = $this->getCustomerStatData();

		/* foreach long en terme de tps car on crée beaucoup de latitude/longitude */
		foreach ($stats as &$stat) {
			if($stat['addr'] != "") {
				$json = $this->geocodeAdress(htmlentities($stat['addr']));
				$stat['lat'] = floatval($json[0]['lat']);
				$stat['lon'] = floatval($json[0]['lon']);
			}
		}


		$json_data = json_encode($stats);

		return $json_data;
	}

	/** getCustomerStatData
	 * fill in geocode table
	 *  FOR MAP CUSTOMERS
	 *  fonction fille pour la carte clients
	 **/
	public function getCustomerMapData()
	{

		$data = $this->getCustomerStatData();
		
		$json_data = json_encode($data);

		return $json_data;
	}
	
	
	/** Mettre dans trait Model **/
    private function checkEntryToModel($model, array $filters)
    {
        $entry = $model->getCollection();
        foreach ($filters as $k => $v) {
            $entry->addFieldToFilter($k, $v);
        }
        if ($entry->getFirstItem()->getId() != null) {
            return true;
        } else {
            return false;
        }
    }


	/** Mettre dans trait Model **/
    private function addEntryToModel($model, $data, $updatedFields)
    {
        foreach ($data as $k => $v) {
            $model->setData($k, $v);
        }
        foreach ($updatedFields as $k => $v) {
            $model->setData($k, $v);
        }
        $model->save();
    }

	/** Mettre dans trait Model **/
    private function updateEntryToModel($model, array $filters, array $updatedFields)
    {
        $entry = $model->getCollection();
        foreach ($filters as $k => $v) {
            $entry->addFieldToFilter($k, $v);
        }
        if (($id = $entry->getFirstItem()->getId()) != null) {
            $model->load($id);
            foreach ($updatedFields as $k => $v) {
                $model->setData($k, $v);
            }
            $model->save();
        } else {
            $this->addEntryToModel($model, $updatedFields);
        }
    }



	/** Mettre dans trait Model **/
    public function addEntryToGeocodeCustomers(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/geocode_customers')),
            $data
        );
    }

	/** Mettre dans trait Model **/
    public function updateEntryToGeocodeCustomers(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/geocode_customers');
        $check = $this->checkEntryToModel($model, $filters);

        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }











	/* FIDELITE */
	/*****************************/
	/*****************/

	private function getOrderAttachments($order)
	{
		$attachments					= \Mage::getModel('amorderattach/order_field')->load($order->getId(), 'order_id');
		$commentaires_ticket			= '|*COM. TICKET*|'."\n".$attachments->getData('commentaires_ticket')."\n";
		$commentaires_interne			= '|*COM. INTERNE*|'."\n".$attachments->getData('commentaires_commande')."\n";
		$commentaires_fraislivraison	= '|*COM. FRAISLIV*|'."\n".$attachments->getData('commentaires_fraislivraison');
		$comments = $remboursement_client.$commentaires_ticket.$commentaires_interne.$commentaires_fraislivraison;

		return $comments;
	}


	//Used in data_clients()
	private function getRelevantComments($order)
	{
	    $orderAttachment = $this->getOrderAttachments($order);

	    return $orderAttachment.$order_comments;
	}

/*
	public function get_list_orderid()
	{
		$orders = \Mage::getResourceModel('sales/order_collection')
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToSelect('increment_id')
			->addAttributeToSelect('created_at')
			->setOrder('increment_id', 'asc');
		$array_orderid = array();
		foreach ($orders as $order) {
			$id					= $order->getIncrementId();
			$date				= date('d/m/Y', strtotime($order->getCreatedAt()));
			$array_orderid[$id] = $date;
		}

		return $array_orderid;
	}
 */


	/** Used in /var/www/html/apdcdev/delivery/modules/clients/views/clients_fidelity.phtml */
	public function data_clients($debut, $fin)
	{
		$data = [];
		/* Format dates */
		//$debut	= date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
		$debut = date('Y-m-d', strtotime(str_replace('/', '-', $debut)));
		//$fin	= date('Y-m-d H:i:s', strtotime('-1 second', strtotime('+1 day', strtotime(str_replace('/', '-', $fin)))));
		$fin	= date('Y-m-d', strtotime('-1 second', strtotime('+1 day', strtotime(str_replace('/', '-', $fin)))));

		$orders = \Mage::getModel('sales/order')->getCollection()
			//->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addFieldToFilter('status', array('nin' => array('canceled', 'holded')))
			//->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin))
			->addAttributeToSort('increment_id', 'DESC');
		//Get info on delivery date
		$orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id = mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
		$orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id = mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));
		$orders->addFilterToMap('ddate', 'mwddate.ddate');
        $orders->addAttributeToFilter('ddate', array(
                'from' => $debut,
                'to' => $fin,
            ));

		foreach ($orders as $order) {
			$status			= $order->getStatusLabel();
			$date_commande	= date('d/m/Y', strtotime($order->getCreatedAt()));
			if (!is_null($order->getDdate())) {
				$date_livraison = date('d/m/Y', strtotime($order->getDdate()));
			} else {
				$date_livraison = 'Non Dispo';
			}
			//Coupon code info
			$afs = array(
					0 => 'Non',
					1 => 'Pour les articles ...',
					2 => 'Pour la livraison ...',
			);
			if ($order->getCouponCode() <> "") {
				$oCoupon	= \Mage::getSingleton('salesrule/coupon')->load($order->getCouponCode(), 'code');
				$oRule		= \Mage::getSingleton('salesrule/rule')->load($oCoupon->getRuleId());
				$coupondata	= "";
				$coupondata	.= "Règle n°".$oRule->getData('rule_id');
				$coupondata	.= ".\n Réduction de ".$oRule->getData('discount_amount');
				$coupondata	.= "de type ".$oRule->getData('simple_action');
				$coupondata	.= ".\n Appliquée au shipping: ".$oRule->getData('apply_to_shipping');
				$coupondata	.= ".\n Livraison gratuite ".$afs[$oRule->getData('simple_free_shipping')].'.';
			} else {
				$coupondata	= "";
			}
			$incrementid		= $order->getIncrementId();
			$nom_client			= $order->getCustomerName().' '.$order->getCustomerId();
			$couponcode			= $order->getCouponCode();
			$couponrule			= $coupondata;
			$total_withship		= $order->getGrandTotal();
			$frais_livraison	= $order->getShippingAmount() + $order->getShippingTaxAmount();
			$total_withoutship	= $total_withship - $frais_livraison;
			$comments			= $this->getRelevantComments($order);
			array_push($data, [
				'status'			=> $status,
				'date_commande'		=> $date_commande,
				'date_livraison'	=> $date_livraison,
				'increment_id'		=> $incrementid,
				'nom_client'		=> $nom_client,
				'Total Produit'		=> $total_withoutship,
				'Frais livraison'	=> $frais_livraison,
				'Total'				=> $total_withship,
				'Coupon Code'		=> $couponcode,
				'Règle Coupon'		=> $couponrule,
				'Commentaires'		=> $comments,
			]);
		}

		return $data;
	}


	/***************** COUPON CLIENT ************/
	/*******************************************/


	/** Used in /var/www/html/apdcdev/delivery/modules/clients/views/clients_coupon.phtml */
	public function data_coupon($debut, $fin)
	{
		$data = [];
		/* Format dates */
		$debut	= date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
		$fin	= date('Y-m-d H:i:s', strtotime('-1 second', strtotime('+1 day', strtotime(str_replace('/', '-', $fin)))));
		$orders = \Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin))
			->addAttributeToFilter('status', array('eq' => \Mage_Sales_Model_Order::STATE_COMPLETE))
			->addAttributeToSort('increment_id', 'DESC');
		foreach ($orders as $order) {
			array_push($data, [
				'increment_id'	=> $order->getIncrementId(),
				'quartier'		=> $order->getStoreName(),
				'Coupon Code'	=> $order->getCouponCode(),
			]);
			arsort($data);
		}
		$data_conso = [];
		foreach ($data as $row) {
			if ($row['Coupon Code']) {
				$data_conso[$row['Coupon Code']][] = $row['increment_id'].' - '.$row['quartier'];
			}
		}

		return $data_conso;
	}



	/****************************
	 * NOTES CLIENTS *****************/
	
	public function end_month($date) 
	{
		$date = strtotime('+1 month', strtotime(str_replace('/', '-', $date)));
		$date = strtotime('-1 second', $date);
		$date = date('Y-m-d', $date);

		return $date;
	}

	public function getNotes($date_debut, $date_fin)
	{
		$date_debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date_debut)));
		$notationClient = \Mage::getModel('sales/order')->getCollection()
			->addAttributeToFilter('created_at', array('from' => $date_debut, 'to' => $date_fin));
		$notationClient->getSelect()->joinLeft('apdc_notation', 'main_table.entity_id = apdc_notation.order_id');
		$notationClient->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id = mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
		$notationClient->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id = mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));

		$result = [];

		foreach ($notationClient as $n) {
			$result[] = [
				'date_creation'		=> date('d/m/Y', strtotime($n->getCreatedAt())),
				'date_livraison'	=> date('d/m/Y', strtotime($n->getDdate())), 
				'increment_id'		=> $n->getData('increment_id'), 
				'nom_client'		=> $n->getCustomerName(),
				'id_client'			=> $n->getCustomerId(),
				'note'				=> $n->getNote(),
				'entity_id'			=> $n->getData('entity_id'),	
			];
		}

		return $result;
	}

	public function histogramme($date_debut, $date_fin)
	{
		$notes = $this->getNotes($date_debut, $date_fin);
		$result = [];
		foreach ($notes as $key => $value) {
			$result[$key] = [
				'notes'			=> intval($value['note']),
				'occurences'		=> 0,
				];

			$occ = array_count_values(array_column($result, 'notes'));

			foreach ($occ as $k => $v) {
				if($result[$key]['notes'] === $k ) { 
					$result[$key]['occurences'] += $v;
				}
			}

		}
		sort($result);
		$json_data = json_encode($result);
		return $json_data;


	}
}
