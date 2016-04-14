<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Dashboard extends AwoDev_AwoRewards_Block_Adminhtml_Widget_Grid_Container {
	public function __construct() {
		// The blockGroup must match the first half of how we call the block, and controller matches the second half
		// ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'awodev_aworewards';
        $this->_controller = 'adminhtml_dashboard';
        $this->_headerText = $this->__('Dashboard');
         
        parent::__construct();
    }
	
	function getStatus() {
		$cache = Mage::app()->getCache();
		if(!($check = $cache->load('awodev_aworewards_version'))) {
			$check = json_encode($this->getVersionUpdate());
			$cache->save($check, 'awodev_aworewards_version', array('awodev_aworewards'), 3600*72); # 3 days
		}
		$check = json_decode($check);
		return $check;
	}
	
	
	function getRules() {
		$list = array();

		$collection = Mage::getModel('awodev_aworewards/rule')
				->getCollection()
				->addFieldToFilter('published',1)
				->addFieldToFilter('rule_type',array('nin'=>array('order','registration','review')))
				->setOrder('rule_name', 'ASC')
				->setOrder('id', 'ASC')
		;
        $collection->getSelect()
				->join(
					array('w' => Mage::helper('awodev_aworewards')->getTable('core_website')),
					"w.website_id = main_table.website_id",
					array('w.name as website_name')
				)
		;
		foreach($collection as $item) {
			$data = $item->getData(); 
			
			$list[] = $data;
		}
		return $list;
	}
	function getCreditList() {
		$list = array();
		$collection = Mage::getModel('awodev_aworewards/credit')
				->getCollection()
				->setOrder('id','desc')
		;
		$collection->getSelect()
                ->join(
					array('u' => Mage::helper('awodev_aworewards')->getTable('customer_entity')),
					"u.entity_id = main_table.user_id",
					array('u.email','u.website_id')
				)
                ->joinLeft(
					array('r' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_rule')),
					'r.id=main_table.rule_id',
					array('r.rule_name')
				)
                ->joinLeft(
					array('p' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_payment')),
					'p.id=main_table.payment_id',
					array('p.payment_details')
				)
			;
		
			
		$fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
		$ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
		$collection->getSelect()
				->join(	array('ce1' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')),
						'ce1.entity_id=u.entity_id', 
						 array('firstname' => 'value')
				)
				->where('ce1.attribute_id='.$fn->getAttributeId()) 
				->join(	array('ce2' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')), 
						'ce2.entity_id=u.entity_id', 
						array('lastname' => 'value')
				)
				->where('ce2.attribute_id='.$ln->getAttributeId()) 
				->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS user_name"));
		$collection->getSelect()->limit( 10 );
		foreach($collection as $item) {
			$data = $item->getData(); 
			$data['str_rule_type'] = empty($data['rule_type']) ? '' : Mage::helper('awodev_aworewards')->vars('rule_type',$data['rule_type']);
			$data['reward'] = !empty($data['points']) ? '('.round($data['points']).')' : $data['payment_details'];
			
			$list[] = $data;
		}
		
		return $list;
		

	}
	
	function getReferralList() {
		$list = array();
		$collection = Mage::getModel('awodev_aworewards/referral')
				->getCollection()
				->setOrder('id','desc')
		;
		$collection->getSelect()
                ->join(
					array('u' => Mage::helper('awodev_aworewards')->getTable('customer_entity')),
					"u.entity_id = main_table.user_id",
					array('u.email AS affiliate_email','u.website_id')
				)
                ->joinLeft(
					array('u2' => Mage::helper('awodev_aworewards')->getTable('customer_entity')),
					'u2.entity_id=main_table.join_user_id',
					array('u2.email AS friend_email')
				)
			;
		
			
		$fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
		$ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
		$collection->getSelect()
				->join(	array('ce1' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')),
						'ce1.entity_id=u.entity_id', 
						 array('firstname' => 'value')
				)
				->where('ce1.attribute_id='.$fn->getAttributeId()) 
				->join(	array('ce2' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')), 
						'ce2.entity_id=u.entity_id', 
						array('lastname' => 'value')
				)
				->where('ce2.attribute_id='.$ln->getAttributeId()) 
				->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS affiliate_name"));
	
	
		$collection->getSelect()
				->joinLeft(	array('cf1' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')),
						'cf1.entity_id=u2.entity_id AND cf1.attribute_id='.$fn->getAttributeId(), 
						 array('firstname' => 'value')
				)
				->joinLeft(	array('cf2' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')), 
						'cf2.entity_id=u2.entity_id AND cf2.attribute_id='.$ln->getAttributeId(), 
						array('lastname' => 'value')
				)
				->columns(new Zend_Db_Expr("CONCAT(`cf1`.`value`, ' ',`cf2`.`value`) AS friend_name"));
		$collection->getSelect()->limit( 10 );
		foreach($collection as $item) {
			$list[] = $item->getData(); 
		}
		return $list;
			
	}
	
	function getLicense() {
		$license = $website = $expiration = null;
		
		$collection = Mage::getModel('awodev_aworewards/license')->getCollection();
			
		foreach($collection as $row) {
			$row = (object)$row->getData();
			if($row->keyname=='license') $license = $row->value;
			elseif($row->keyname=='expiration') $expiration = $row->value;
			elseif($row->keyname=='website') $website = explode('|',$row->value);
		}
		return (object) array('l'=>$license,'url'=>!empty($website) ? current($website) : '','exp'=>$expiration);
	}

	
	
	function getVersionUpdate() {
		$path = 'sites/default/files/extstatus/aworewardsmg.xml';
		$domain = 'awodev.com';
	 	$url = 'http://'.$domain.'/'.$path;
		$data = '';
		$check = array();
		$check['connect'] = 0;
		$check['current_version'] = Mage::helper('awodev_aworewards')->getExtensionVersion();

		//try to connect via cURL
		if(function_exists('curl_init') && function_exists('curl_exec')) {
			$ch = @curl_init();
			
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, 0);
			//http code is greater than or equal to 300 ->fail
			@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			//timeout of 5s just in case
			@curl_setopt($ch, CURLOPT_TIMEOUT, 5);
						
			$data = @curl_exec($ch);
						
			@curl_close($ch);
		}

		//try to connect via fsockopen
		if(function_exists('fsockopen') && $data == '') {

			$errno = 0;
			$errstr = '';

			//timeout handling: 5s for the socket and 5s for the stream = 10s
			$fsock = @fsockopen($domain, 80, $errno, $errstr, 5);
		
			if ($fsock) {
				@fputs($fsock, "GET /".$path." HTTP/1.1\r\n");
				@fputs($fsock, "HOST: ".$domain."\r\n");
				@fputs($fsock, "Connection: close\r\n\r\n");
        
				//force stream timeout...
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);
				 
				$get_info = false;
				while (!@feof($fsock))
				{
					if ($get_info)
					{
						$data .= @fread($fsock, 1024);
					}
					else
					{
						if (@fgets($fsock, 1024) == "\r\n")
						{
							$get_info = true;
						}
					}
				}        	
				@fclose($fsock);
				
				//need to check data cause http error codes aren't supported here
				if(!strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>')) {
					$data = '';
				}
			}
		}

	 	//try to connect via fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen') && $data == '') {
		
			//set socket timeout
			ini_set('default_socket_timeout', 5);
			
			$handle = @fopen ($url, 'r');
			
			//set stream timeout
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			
			$data	= @fread($handle, 1000);
			
			@fclose($handle);
		}
						
		if( $data && strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>') ) {
			preg_match('/\<version\>([^<]*).*?\<released\>([^<]*)/',$data,$matches);
			
			$check['version'] = $matches[1];
			$check['released'] = $matches[2];
			$check['connect'] 		= 1;
			$check['enabled'] 		= 1;
			$check['current'] 		= version_compare( $check['current_version'], $check['version'] );
		}
		
		return (object)$check;
	 }
	function getLocalBuild() {
		$versionString	= Mage::helper('awodev_aworewards')->getExtensionVersion();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[2]) )
		{
			return $tmpArray[2];
		}
		
		// Unknown build number.
		return 0;
	}
	function getLocalVersion() {
		$versionString	= Mage::helper('awodev_aworewards')->getExtensionVersion();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[0] ) && isset( $tmpArray[1] ) )
		{
			return doubleval( $tmpArray[0] . '.' . $tmpArray[1] ); 
		}
		return 0;
	}

	
}