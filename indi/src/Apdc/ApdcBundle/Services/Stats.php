<?php

namespace Apdc\ApdcBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;

include_once '../../app/Mage.php';

define('FLOAT_NUMBER', 2);

class Stats
{
    use Helpers\Order;
    use Helpers\Model;

	private $em;

	public function __construct(ObjectManager $em)
	{
		$this->em = $em;
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

	/**
	 *	Fonction pour les stats des clients avec des commandes + le mapping client
	 *	Jointure entre sales_order / address / customer_entity et geocode
	 */
	public function getCustomerStatData()
	{
		set_time_limit(0);

		$data = [];
		$orders = \Mage::getModel('sales/order')->getCollection()//->setOrder('entity_id', 'DESC')
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToFilter('status', array('in' => array(\Mage_Sales_Model_Order::STATE_COMPLETE, \Mage_Sales_Model_Order::STATE_CLOSED)));

		$orders->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id', array('postcode', 'street', 'city', 'telephone'));
		$orders->getSelect()->joinLeft('customer_entity', 'sales_flat_order_address.customer_id = customer_entity.entity_id', array('customer_created_at'=> 'customer_entity.created_at'));
		$orders->getSelect()->joinLeft('geocode' ,'sales_flat_order_address.street = geocode.former_address', array('address','lat', 'long'));
		$orders->getSelect()->joinLeft('amasty_amorderattach_order_field',"main_table.entity_id = amasty_amorderattach_order_field.order_id",array('commentaires_client'=>'amasty_amorderattach_order_field.commentaires_client'));

		$orders->addAttributeToFilter('address_type', 'shipping');
		$orders->getSelect()->columns('COUNT(DISTINCT main_table.entity_id) AS nb_order')
			->columns('GROUP_CONCAT(DISTINCT IF(commentaires_client>"",CONCAT(main_table.increment_id, ": " , " - ",commentaires_client),"") ORDER BY main_table.entity_id ASC SEPARATOR " // ") AS commentaires')
			->columns('SUM(base_grand_total) AS amount_total, AVG(base_grand_total) AS average_orders, STDDEV(base_grand_total) AS std_dev_orders, MAX(base_grand_total) AS max_orders')
			->columns('MAX(main_table.created_at) AS last_order')
			->group('customer_id');

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
					'addr'				=> $order->getAddress(), // from geocode
					'code_postal'		=> $order->getPostcode(),
					'ville'				=> $order->getCity(),
					//'Créé dans'		=> $order->getCreatedIn(),
					'email'				=> $order->getCustomerEmail(),
					'telephone'			=> $order->getTelephone(),
					'lat'				=> $order->getData('lat'),
					'lon'				=> $order->getData('long'),
					'commentaires'		=> $order->getCommentaires(),
				]);
		}

		unset($orders);

		return $data;
	}

	/**
	 * Fonction pour les stats des clients avec 0 commande
	 */
	public function getCustomerNoOrderStatData()
	{
		set_time_limit(0);

		$data = [];

		//Add customer who never ordered
		$customers = \Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
		$customers->getSelect()->joinLeft(
			array('order' => \Mage::getSingleton('core/resource')->getTableName('sales/order')),
			'order.customer_id = e.entity_id',
			array(
				'last_order_date'	=> 'MAX(order.created_at)',	
			)
		);

		$customers->groupByAttribute('entity_id')->getSelect()->having('last_order_date IS NULL');

		foreach ($customers as $customer) {
			array_push($data, [
				'nom_client'		=> $customer->getFirstname().' '.$customer->getLastname(),
				'id_client'			=> $customer->getCustomerId(),
				'nb_commande'		=> 0,
				'inscription'		=> \Mage::helper('core')->formatDate($customer->getCreatedAt(), 'short', false),
				'code_postal'		=> $customer->getCreatedIn(),
				'email'				=> $customer->getEmail(),
			]);
		}

	 	unset($customers);
	 
		return $data;
	}

    /**	Fonction fille pour le mapping client
     *	Convertit simplement la data de la fonction mère en tableau json
     *	Ce json sera utilisé dans le controlleur de mapping clients
     *	Puis parsé dans la vue twig afin d'afficher les clients sur la carte */
    public function getCustomerMapData()
    {
        $data = $this->getCustomerStatData();

        $json_customers = json_encode($data);
        return $json_customers;
    }
    
    /**	Fonction identique à getCustomerStatData
     *	MAIS pas de jointure sur geocode
     *	Utilisé pour l'ajout, dans la table geocode, des new CLIENTS
     */
    public function getNewCustomerData()
    {
        $data = [];
        $orders = \Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
            ->addAttributeToFilter('status', array('in' => array(\Mage_Sales_Model_Order::STATE_COMPLETE, \Mage_Sales_Model_Order::STATE_CLOSED)));

        $orders->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id', array('postcode', 'street', 'city'));
        $orders->getSelect()->joinLeft('customer_entity', 'sales_flat_order_address.customer_id = customer_entity.entity_id', array('customer_created_at' => 'customer_entity.created_at'));
        $orders->addAttributeToFilter('address_type', 'shipping');
        $orders->getSelect()->columns('COUNT(*) AS nb_order')
            ->columns('SUM(base_grand_total) AS amount_total, AVG(base_grand_total) AS average_orders, STDDEV(base_grand_total) AS std_dev_orders, MAX(base_grand_total) AS max_orders')
            ->columns('MAX(main_table.created_at) AS last_order')
            ->group('sales_flat_order_address.customer_id');

        // data[] n'a que les champs utiles pour l'update de la table geocode
        foreach ($orders as $order) {
            /* Corriger jointure sur customer id NULL*/
            if ($order->getCustomerId() != null) {
                array_push($data, [
                'address'					=> $order->getStreet(), // cette addresse sera par la suite modifiée dans la fonction cleanAddrForMap()
                'postcode'					=> $order->getPostcode(),
                'city'						=> $order->getCity(),
                'lat'						=> '',
                'long'						=> '',  // lat et long seront géocodés dans la fonction geocode();
                'former_address'			=> $order->getStreet(), // CELLE CI NE SERA PAS MODIF CAR UTILE A JOINTURE ENTRE GEOCODE ET SALES_ORDER_ADDRESS
                'id_customer'				=> $order->getCustomerId(), // id pouvant faire office de jointure entre les tables geocode et customer_entity
            ]);
            }
        }
        return $data;
    }

    /**	Fonction utilisée pour la MAJ de la table geocode
     *	La jointure entre sales_flat_order_adress.street et geocode.former_address (checker la jointure de la fonction getCustomerStatData() )
     *	se fait sur des adresses à syntaxe incorrecte
     *	On supprime les virgules et tout le contenu des adresses après les \n , les tirets, 'interphone' , 'code'
     *	Strtr sur tous les caracteres accentués et les types de voies approximatifs
     */
    public function cleanAddrForMap()
    {
        $data = $this->getNewCustomerData();

        $bad_chars	= ['À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'];
        $good_chars = ['A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y'];

        foreach ($data as &$v) {
            if (strpos($v['address'], "\n")) {
                $cut_pos		= strpos($v['address'], "\n");
                $v['address']	= substr($v['address'], 0, $cut_pos);
            }
            if (strpos($v['address'], ' '."-")) {
                $cut_pos_two	= strpos($v['address'], ' '."-");
                $v['address']	= substr($v['address'], 0, $cut_pos_two);
            }
            if (strpos($v['address'], "code")) {
                $cut_pos_three	= strpos($v['address'], "code");
                $v['address']	= substr($v['address'], 0, $cut_pos_three);
            }
            if (strpos($v['address'], "interphone")) {
                $cut_pos_four	= strpos($v['address'], "interphone");
                $v['address']	= substr($v['address'], 0, $cut_pos_four);
            }

            $v['address'] = str_replace(",", "", $v['address']);
            $v['address'] = strtr($v['address'], array_combine($bad_chars, $good_chars));
        }

        return $data;
    }


    private function geocodeAdress($adress)
    {

        $adress = urlencode(htmlentities($adress));
        $query	= 'https://nominatim.openstreetmap.org/search?format=json&street='.$adress.'&city=Paris&country=France&countrycodes=fr';

        $curl = new \Varien_Http_Adapter_Curl();
        $curl->setConfig(['timeout' => 15]);
        $curl->write(
            \Zend_Http_Client::GET, 
            $query,
            '1.1',
            [   'Content-Type: application/json', 
                'Accept: application/json',
                'User-Agent:' . $_SERVER['HTTP_USER_AGENT']
            ]
        );
        $response = \Zend_Http_Response::extractBody($curl->read());
        $curl->close();

        $json	= json_decode($response, true);

        return $json;
    }

    public function addLatAndLong()
    {
        $data = $this->cleanAddrForMap();

        /* foreach long en terme de tps car on crée beaucoup de latitude/longitude */
        /* Attention à ne pas abuser de la limite des encodages lat/long PAR JOUR :) */
        /* sinon toutes les lat long seront a zero pour la journée */
        foreach ($data as &$v) {
            if ($v['address'] != "") {
                $json = $this->geocodeAdress(htmlentities($v['address']));
                $v['lat']	= floatval($json[0]['lat']);
                $v['long']	= floatval($json[0]['lon']);
            }
        }
     
        return $data;
    }

    /***************/
    /** Comparaison entre les sales_flat_order_address.customer_id ET geocode.whoami
     *	pour afficher ou non le bouton submit de la MAJ map clients */

    public function compareCustomers()
    {
        $orderIds = \Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
            ->addAttributeToFilter('status', array('in' => array(\Mage_Sales_Model_Order::STATE_COMPLETE, \Mage_Sales_Model_Order::STATE_CLOSED)));
        $orderIds->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id', array('customer_id'));
        $orderIds->getSelect()->group('sales_flat_order_address.customer_id');
        $orders = [];
        $oAddrs = [];
        
        $geocodeIds = \Mage::getModel('pmainguet_delivery/geocode_customers')->getCollection();
        $geocodes = [];
        $gAddrs = [];

        foreach ($orderIds as $orderId) {
            $orders[] = $orderId->getData('customer_id');
            $oAddrs[] = $orderId->getData('street');
        }

        $cpt = 0;
        foreach ($geocodeIds as $geocodeId) {
            $geocodes[] = $geocodeId->getData('whoami');
            $gAddrs[$geocodeId->getData('whoami')][$cpt] = $geocodeId->getData('former_address');
            $cpt++;
        }

        $countCustomers = array_count_values($geocodes);

        if (count($orders) !== ($countCustomers['CUSTOMER'])) {
            return true;
        } elseif (!empty(array_diff($oAddrs, $gAddrs['CUSTOMER']))) {
            return true;
        } else {
            return false;
        }
    }

    /******** MERCHANTS ****/

    /** Fonction utilisée pour l'ajout de nouveaux merchants dans geocode */
    public function getNewShopData()
    {
        $data = [];
        $merchants = $this->getUngeocodeMerchants();
        foreach ($merchants as $merchant) {
            array_push($data, [
                'address'			=> $merchant->getStreet(), // peut potentiellement etre modifié pour respecter la syntaxe de l'encodage géographique
                'postcode'			=> $merchant->getPostcode(),
                'city'				=> $merchant->getCity(),
                'lat'				=> '', // lat et long seront encodé par la suite
                'long'				=> '',
                'former_address'	=> $merchant->getStreet(), // sera utilisé pour la jointure de la fonction getMerchantsStatData. NEST PAS MODIFIE
                'id_shop'			=> $merchant->getData('id_shop'),
            ]);
        }

        $bad_chars	= ['À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'];
        $good_chars = ['A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y'];


        /* c'est comme la fonction addLatAndLong(), pour les commercants cette fois */
        foreach ($data as &$content) {
            $content['address'] = strtr($content['address'], array_combine($bad_chars, $good_chars));
            
            if ($content['address'] != "") {
                $json = $this->geocodeAdress(htmlentities($content['address']));
                $content['lat']		= floatval($json[0]['lat']);
                $content['long']	= floatval($json[0]['lon']);
            }
        }

        return $data;
    }

    /**	Get All Merchants Joined with Geocode Table */
    public function getMerchantsStatData()
    {
        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection()
        ->addFieldToFilter('enabled', 1)
        ->addFieldToSelect(array('id_shop','enabled','name','street','postcode','city','code','category_thumbnail','stores', 'phone', 'timetable', 'shop_type'));
        $shops->getSelect()->joinLeft(
            array('geocode' => \Mage::getModel('pmainguet_delivery/geocode_customers')->getResource()->getTable('pmainguet_delivery/geocode_customers')),
            "main_table.id_shop = geocode.id_shop",
            array('geocode.lat','geocode.long')
        );
        $shops->addFilterToMap('lat','geocode.lat');
        $shops->addFilterToMap('lon', 'geocode.lon');

        $shops_array = $shops->load()->toArray();
        foreach ($shops_array as &$shop) {
            foreach ($shop['timetable'] as &$t) {
                $t = "<br> -> " . $t;
            }
        }

		return json_encode($shops_array);
    }

    //Filter getMerchantsStatData() for null or 0 lat/long
    public function getUngeocodeMerchants()
    {
        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection()
        ->addFieldToFilter('enabled', 1)
        ->addFieldToSelect(array('id_shop','enabled','name','street','postcode','city','code','category_thumbnail','stores'));
        $shops->getSelect()->joinLeft(
            array('geocode' => \Mage::getModel('pmainguet_delivery/geocode_customers')->getResource()->getTable('pmainguet_delivery/geocode_customers')),
            "main_table.id_shop = geocode.id_shop",
            array('geocode.lat','geocode.long')
        );
        $shops->addFilterToMap('lat', 'geocode.lat');
        $shops->addFilterToMap('lon', 'geocode.lon');
        $shops->addFieldToFilter(
            array('lat','lat'),
            array(
                array('null'=>''),
                array('eq'=>0)
            )
        );
        return $shops->load();
    }


    
    /********************************************************/
    /* FIDELITE */
    /*****************************/
    /*****************/

    private function getOrderAttachments($order)
    {
        $attachments					= \Mage::getModel('amorderattach/order_field')->load($order->getId(), 'order_id');
        $commentaires_commercant		= '|*COM. COMMERCANT*|'."\n".$attachments->getData('commentaires_commercant')."\n";
        $commentaires_client	        = '|*COM. CLIENT*|'."\n".$attachments->getData('commentaires_client');
        $comments = $remboursement_client.$commentaires_commercant.$commentaires_interne.$commentaires_client;

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
            
            //Coupon Code
            $coupondata	= $couponcode = "";
            if (floatval($order->getBaseDiscountAmount())<>0) {
                $coupondata	.= "Réduction de ".(-floatval($order->getBaseDiscountAmount()))."€.";
            } else {
                if ($order->getBaseShippingAmount()==0) {
                    $coupondata	.= "Livraison gratuite.";
                }
            }
            if ($order->getCouponCode()<>"") {
                $couponcode	= $order->getCouponCode();
            } else {
                if (floatval($order->getBaseDiscountAmount())<>0) {
                    $couponcode = "Discount sans coupon";
                }
            }

            $incrementid		= $order->getIncrementId();
            $nom_client			= $order->getCustomerName().' '.$order->getCustomerId();
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
        $debut	= date('Y-m-d', strtotime(str_replace('/', '-', $debut)));
        $fin	= date('Y-m-d', strtotime('-1 second', strtotime('+1 day', strtotime(str_replace('/', '-', $fin)))));
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
                'Discount'		=> -floatval($order->getBaseDiscountAmount()),
                'client'		=> $order->getData('customer_firstname').' '.$order->getData('customer_lastname'),
                'created_at'	=> date('d-m-Y', strtotime($order->getData('created_at'))),
                'shipping_amount' => floatval($order->getBaseShippingAmount()),
            ]);
            arsort($data);
        }
        $data_conso = [];
        foreach ($data as $row) {
            if ($row['shipping_amount']==0 || floatval($row['Discount'])<>0) {
                if (floatval($row['shipping_amount'])==0) {
                    if ($row['Coupon Code']=="") {
                        $row['Coupon Code']='Livraison gratuite sans coupon';
                    }
                } else {
                    if ($row['Coupon Code']=="") {
                        $row['Coupon Code']='Discount sans coupon';
                    }
                }
                $data_conso[$row['Coupon Code']][] = [
                    'order'			=> $row['increment_id'].' '.$row['quartier'],
                    'customer'		=> $row['client'],
                    'created_at'	=> $row['created_at'],
                ];
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
                if ($result[$key]['notes'] === $k) {
                    $result[$key]['occurences'] += $v;
                }
            }
        }
        sort($result);
        $json_data = json_encode($result);
        return $json_data;
    }

	public function getProductEvolutionByDateAndSku($date_debut, $date_fin, $sku)
	{
        $date_debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date_debut)));
        $date_fin = date('Y-m-d H:i:s', strtotime('+1 day', strtotime('-1 second', strtotime(str_replace('/', '-', $date_fin)))));

        $resource = \Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $tableName = $resource->getTableName('indi_product_history');

        $query = 'SELECT * FROM ' . $tableName . ' WHERE createdOn BETWEEN "' . $date_debut . '" AND "' . $date_fin .'" AND sku= "' . $sku . '"';

        $results = $readConnection->fetchAll($query);
        return $results;
	}

    public function cleanUpProductEvolutionByDateAndSku($date_debut, $date_fin, $sku)
    {
        $products = $this->getProductEvolutionByDateAndSku($date_debut, $date_fin, $sku);

        $cleanProducts = [];
        foreach ($products as $product) {

            $createdOn = date('Y-m-d', strtotime($product['createdOn']));

            $cleanProducts[$createdOn] = [
                $product['sku'] => $product['prix_public'],
                'date'     => $createdOn,
            ];
        }

        $cpt = 0;
        foreach ($cleanProducts as $key => $value) {
            $cleanProducts[$cpt] = $value;
            unset($cleanProducts[$key]);
            $cpt++;
        }

        return array_values($cleanProducts);
    }

    public function getMerchantProductPriceVariation($date_debut, $date_fin)
    {
        $date_debut = date('Y-m-d', strtotime(str_replace('/', '-', $date_debut)));
        $date_fin = date('Y-m-d', strtotime(str_replace('/', '-', $date_fin)));
        
        $res = [];
        $cpt = 0;

        $orders = \Mage::getModel('pmainguet_delivery/refund_pricevariation')->getCollection();
        $orders->addFieldToFilter('delivery_date', array('from' => $date_debut, 'to' => $date_fin));

        foreach ($orders as $order) {
            $res[$cpt] = [
                'order_id'      => $order->getData('order_id'),
                'order_date'    => $order->getData('order_date'),
                'delivery_date' => $order->getData('delivery_date'),
                'merchant_id'   => $order->getData('merchant_id'),
                'merchant_name' => $order->getData('merchant'),
                'excess'        => $order->getData('merchant_excess'),
                'lack'          => $order->getData('merchant_lack'),
            ];

            $cpt++;
        }

        return $res;
    }

    public function getMerchantQuarterLocation()
    {
        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
        $shops->addFieldToFilter('enabled', ['eq' => 1]);
        $S = \Mage::helper('apdc_commercant')->getStoresArray("storeid");
        
        $tmp = [];
        foreach ($shops as $shop) {
            $stores = explode(',', $shop->getStores());
            foreach ($stores as $id) {
                $storeinfo = $S[$id];
                if (!isset($tmp[$storeinfo['store_id']][$shop->getData('id_attribut_commercant')])) {
                    $tmp[$storeinfo['store_id']][$shop->getData('id_attribut_commercant')] = [
                        'id'        => $shop->getData('id_attribut_commercant'),
                        'name'      => $shop->getName(),
                        'shop_type' => $shop->getShopType(),
                        'store'     => $storeinfo['name'],
                        'store_id'  => $storeinfo['store_id'],         
                    ];
                }
            }
        }

        foreach ($tmp as $store_id => $shops) {
            foreach ($shops as $shop_id => $shop) {
                $results["stores"][$store_id] = $shop['store'];
                $results["merchants"][$shop_id]['name'] = $shop['name'];
                $results["merchants"][$shop_id]['shop_type'] = $shop['shop_type'];
                $results["merchants"][$shop_id]['stores'][] = $shop['store_id'];
            }
        }

        asort($results['stores'], SORT_NATURAL);

        return $results;
    }

    public function getCommentsHistory($order_id_min = 0, $order_id_max = 9999999999, $date_debut = '01/01/0000', $date_fin = '31/12/9999')
    {
        $comments = \Mage::getModel('pmainguet_delivery/indi_commenthistory')->getCollection();
        $comments->getSelect()->joinLeft(['commenttype' => \Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/indi_commenttype')], 'commenttype.type = main_table.comment_type', ['comment_label' => 'commenttype.label']);
        $comments->getSelect()->joinLeft(['apdcshop' => \Mage::getSingleton('core/resource')->getTableName('apdc_commercant/shop')], 'apdcshop.id_attribut_commercant = main_table.merchant_id', ['merchant_name' => 'apdcshop.name']);
        $comments->getSelect()->joinLeft(['sales_order' => \Mage::getSingleton('core/resource')->getTableName('sales/order')], 'sales_order.increment_id = main_table.order_id', ['customer_firstname' => 'sales_order.customer_firstname', 'customer_lastname' => 'sales_order.customer_lastname']);

        $comments->addFieldToFilter('order_id', ['from' => $order_id_min, 'to' => $order_id_max]);

        $date_debut = date('Y-m-d', strtotime(str_replace('/', '-', $date_debut)));
        $date_fin = date('Y-m-d', strtotime(str_replace('/', '-', $date_fin)));

        $comments->addFieldToFilter('main_table.created_at', ['from' => $date_debut, 'to' => $date_fin]);

        $res = [];

        foreach ($comments as $comment) {
            array_push($res, [
                'created_at'            => $comment->getData('created_at'),
                'updated_at'            => $comment->getData('updated_at'),
                'author'                => $comment->getData('author'),
                'comment_type'          => $comment->getData('comment_type'),
                'comment_label'         => $comment->getData('comment_label'),
                'comment_text'          => $comment->getData('comment_text'),
                'order_id'              => $comment->getData('order_id'),
                'merchant_id'           => $comment->getData('merchant_id'),
                'merchant_name'         => $comment->getData('merchant_name'),
                'customer_firstname'    => $comment->getData('customer_firstname'),
                'customer_lastname'     => $comment->getData('customer_lastname'),
            ]);
        }
        

        return $res;
    }

    public function getCommentsType()
    {
        $types = \Mage::getModel('pmainguet_delivery/indi_commenttype')->getCollection();
        $res = [];

        foreach ($types as $t) {
            array_push($res, [
                'type'      => $t->getData('type'),
                'label'     => $t->getData('label'),
            ]);
        }

        return $res;
    }
}
