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

	public function stats_clients()
	{
		$data = [];
		$orders = \Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToFilter('status', array('eq' => \Mage_Sales_Model_Order::STATE_COMPLETE));
		$orders->getSelect()->columns('COUNT(*) AS nb_order')
			->columns('SUM(base_grand_total) AS amount_total')
			->columns('MAX(updated_at) AS last_order')
			->group('customer_id');
		foreach ($orders as $order) {
			$customer	= \Mage::getModel('customer/customer')->load($order->getCustomerId());
			$dataadd	= \Mage::getModel('sales/order_address')->load($order->getShippingAddressId());
			$address	= $dataadd->getStreet()[0].' '.$dataadd->getPostcode().' '.$dataadd->getCity();
			array_push($data, [
					'Nom Client'		=> $order->getCustomerName(),
					'Nb Commande'		=> $order->getNbOrder(),
					'Total'				=> round($order->getAmountTotal(), FLOAT_NUMBER, PHP_ROUND_HALF_UP),
					'Dernière commande'	=> date('d/m/Y', strtotime($order->getLastOrder())),
					'Mail client'		=> $order->getCustomerEmail(),
					'Rue'				=> $dataadd->getStreet()[0],
					'Code Postal'		=> $dataadd->getPostcode(),
					'Date Inscription'	=> \Mage::helper('core')->formatDate($customer->getCreatedAt(), 'short', false),
					'Créé dans'			=> $customer->getCreatedIn(),
				]);
		}
		//Add customer who never ordered
		$customers = \Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToSelect('*');
		foreach ($customers as $customer) {
			$key = array_search($customer->getEmail(), $this->array_columns($data, 'Mail client'));
			if ($key == false) {
				array_push($data, [
					'Nom Client'		=> $customer->getFirstname().' '.$customer->getLastname(),
					'Nb Commande'		=> 0,
					'Total'				=> 0,
					'Dernière commande' => 'NA',
					'Mail client'		=> $customer->getEmail(),
					'Rue'				=> "NA",
					'Code Postal'		=> "NA",
					'Date Inscription'	=> \Mage::helper('core')->formatDate($customer->getCreatedAt(), 'short', false),
					'Créé dans'			=> $customer->getCreatedIn(),
				]);
			}
		}
		return $data;
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
		$debut	= date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
		$fin	= date('Y-m-d H:i:s', strtotime('-1 second', strtotime('+1 day', strtotime(str_replace('/', '-', $fin)))));
		$orders = \Mage::getModel('sales/order')->getCollection()
			//->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addFieldToFilter('status', array('nin' => array('canceled', 'holded')))
			->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin))
			->addAttributeToSort('increment_id', 'DESC');
		//Get info on delivery date
		$orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id = mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
		$orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id = mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));
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


	/****************************************************************
	 *
	 * ************************************************************/

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

    public function addEntryToApdcNotation(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('apdc_notation/notation')),
            $data
        );
    }

    public function updateEntryToApdcNotation(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('apdc_notation/notation');
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

/************************************************************************************
 *	
 *********************************************************************************/



	public function end_month($date) 
	{
		$date = strtotime('+1 month', strtotime(str_replace('/', '-', $date)));
		$date = strtotime('-1 second', $date);
		$date = date('Y-m-d', $date);

		return $date;
	}

	/**	Plus interessant de lier sales_flat_order (table majeure) avec apdc_notation (table mineure)
	 *	que l'inverse ?
	 *
	 *	La correlation entre les deux tables est le numero de commande
	 *	"increment_id" en majeur et "order_id" en mineur
	 *
	 */
	public function getNotes($date_debut, $date_fin)
	{
		$date_debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date_debut)));
		$notationClient = \Mage::getModel('sales/order')->getCollection()
			->addAttributeToFilter('created_at', array('from' => $date_debut, 'to' => $date_fin));
		$notationClient->getSelect()->joinLeft('apdc_notation', 'main_table.increment_id = apdc_notation.order_id');
		$notationClient->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id = mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
		$notationClient->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id = mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));



		$result = [];
		foreach ($notationClient as $n) {
			$result[] = [
				'date_creation'		=> date('d/m/Y', strtotime($n->getCreatedAt())),
				'date_livraison'	=> date('d/m/Y', strtotime($n->getDdate())), 
				'increment_id'		=> $n->getData('increment_id'), 
				'nom_client'		=> $n->getCustomerName(),
				'note'				=> $n->getNote(),
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
