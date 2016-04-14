<?php

class AwoDev_AwoRewards_Helper_Data extends Mage_Core_Helper_Abstract {
 
	public function storeurl($user_id=null) {
		if ($user_id == null) $user_id = Mage::getSingleton('customer/session')->getCustomerId();
		$user_id = (int)$user_id;
		if($user_id<=0) return;
		
		return Mage::app()->getStore(Mage::getModel("customer/customer")->load($user_id)->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
	}
	
	public function getExtensionVersion() {
		return (string) Mage::getConfig()->getNode()->modules->AwoDev_AwoRewards->version;
	}
	
	public function getMenu() {
		$current = Mage::helper('core/url')->getCurrentUrl();//echo $current;
		$menu = array(
			'dashboard'=>array('label'=>'Dashboard','url'=>Mage::helper("adminhtml")->getUrl('*/adminhtml_dashboard'),),
			'rule'=>array('label'=>'Rules','url'=>Mage::helper("adminhtml")->getUrl('*/adminhtml_rule'),),
			'invitation'=>array('label'=>'Invitation','url'=>Mage::helper("adminhtml")->getUrl('*/adminhtml_invitation'),),
			'referral'=>array('label'=>'Referrals','url'=>Mage::helper("adminhtml")->getUrl('*/adminhtml_referral'),),
			'credit'=>array('label'=>'Credit History','url'=>Mage::helper("adminhtml")->getUrl('*/adminhtml_credit'),),
			'payment'=>array('label'=>'Payment History','url'=>Mage::helper("adminhtml")->getUrl('*/adminhtml_payment'),),
			'config'=>array('label'=>'Configuration','url'=>Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/awodev_aworewards'),),
		);
		foreach($menu as &$item) $item['is_current'] = $current==$item['url'] ? true : false;

		return $menu;
	}
	
	public function vars($type,$item=null,$excludes=null) {
		$vars = array(
			'rule_type' => array(
				'registration'=>$this->__( 'Registration' ),
				'review'=>$this->__( 'Product Review' ),
				'order'=>$this->__( 'Order' ),
				'facebook_like'=>$this->__('Like our Facebook FanPage'),
				'facebook_wall'=>$this->__('Post to Facebook Wall'),
				'twitter_tweet'=>$this->__('Tweet About Us'),
				'twitter_follow'=>$this->__('Follow us on Twitters'),
			),
			'customer_type' => array(
				'everyone'=>$this->__( 'Everyone' ),
				'sponsor'=>$this->__( 'Sponsor' ),
				'friend'=>$this->__( 'Friend' ),
			),
			'credit_type' => array(
				'mage_coupon'=>$this->__( 'Instant' ),
				'points'=>$this->__( 'Points' ),
			),
			'order_min_type' => array(
				'each'=>$this->__( 'Per Order' ),
				'all'=>$this->__( 'All Orders' ),
			),
			'published' => array(
				'1'=>$this->__( 'Published' ),
				'-1'=>$this->__( 'Unpublished' ),
			),
			'status' => array(
				'active'=>$this->__( 'Active' ),
				'inactive'=>$this->__( 'Inactive' ),
			),
			'referral_status' => array(
				'reg'=>$this->__( 'Registered' ),
				'unreg'=>$this->__( 'Unregistered' ),
			),
			'invitation_type' => array(
				'email'=>$this->__( 'Email' ),
				'facebook'=>$this->__( 'Facebook' ),
				'twitter'=>$this->__( 'Twitter' ),
			),
			'payment_type' => array(
				'mage_coupon'=>$this->__( 'Coupon Code' ),
				'paypal'=>$this->__( 'Paypal' ),
			),
			
		);
		
		if(isset($vars[$type])) {
			if(isset($item)) { if(isset($vars[$type][$item])) return $vars[$type][$item]; else return ''; }
			else return $vars[$type];
		}
		
		//if(isset($vars[$type])) {
		//	if(isset($item)) { if(isset($vars[$type][$item])) return $vars[$type][$item]; else return ''; }
		//	else {
		//		$vars_to_return =  $vars[$type];
		//		if(!empty($excludes)) {
		//			if(!is_array($excludes)) $excludes = array($excludes);
		//			
		//		}
		//		return $vars_to_return;
		//	}
		//}
	}
	

	
	public function getWebsites() {
		$websites = Mage::app()->getWebsites();
		$rtn = '';
		foreach (Mage::app()->getWebsites() as $website) {
			$rtn[$website->getData('website_id')] = $website->getData('name');
		}
		return $rtn;
	}
	
	public function getStores($website_id) {
		$websites = Mage::app()->getWebsites();
		$stores = array(0=>Mage::helper('awodev_aworewards')->__( 'Global' ));
		foreach (Mage::app()->getWebsites() as $website) {
			if((int)$website_id == (int)$website->getData('website_id')) {
				foreach ($website->getGroups() as $group) {
					$storeobj = $group->getStores();
					foreach ($storeobj as $store) {
						$stores[$store->getData('store_id')] = $group->getData('name').' - '.$store->getData('name');
					}
				}
			}
		}
		return $stores;
	}
    public function isCustomerOrdered ($customer_id = null) {
		if ($customer_id == null) $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
		if(empty($customer_id)) return false;

		$collection = Mage::getModel("sales/order")
					->getCollection()
					->addAttributeToFilter('customer_id',$customer_id);
		
		return $collection->getData() ? true : false;
	}
	
	public function br2nl($string){ return preg_replace('/<br.*?\/?>/i',chr(13).chr(10),$string); } 
	
	
	public function simple_number_encrypt($number) { return strtr(base64_encode((double)$number*4282225.87),array('+'=>'.','='=>'-','/'=>'~')); }
	public function simple_number_decrypt($data){ return ((double)base64_decode(strtr($data,array('.' => '+','-' => '=','~' => '/'))))/4282225.87; }
	public function simple_encrypt($text) {
		$salt ='9:jfjrsf7A`*1qwrOVBWX';
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}
	public function simple_decrypt($text) {
		$salt ='9:jfjrsf7A`*1qwrOVBWX';
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}
	
	public function registration_link($user_id=null) { 
		$user_id = (int)$user_id;
		if(empty($user_id)) $user_id = (int) Mage::getSingleton('customer/session')->getCustomerId();
		$customer = Mage::getModel("customer/customer")->load($user_id)->getData();
		if(empty($customer)) return '';
	
		$is_url_short  = (int) Mage::getStoreConfig('awodev_aworewards/invitation/urlshort_enabled');
		$return_url = '';
		
		$userrow = Mage::getModel("awodev_aworewards/user")->load($user_id,'user_id');

		$userrowdata = $userrow->getData();
		if(empty($userrowdata)) {
			//$url = Mage::app()->getStore($customer['store_id'])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
			//$url =  Mage::getUrl('awodev_aworewards/register',array('id'=>urlencode($this->simple_number_encrypt((int)$user_id)))) ;
			
			$return_url = $url_real = Mage::getUrl('awodev_aworewards/register',array('id'=>urlencode($this->simple_number_encrypt((int)$user_id))));
			$url_short = '';
			if(!empty($is_url_short)) {
			
				$oauth = Mage::helper('awodev_aworewards/oauth_google');
				$oauth->init();
				$url_short = $oauth->get_urlshort($url_real);
				if(!empty($url_short)) $return_url = $url_short;
			}
			$url_short_str = empty($url_short) ? null : '"'.$url_short.'"';
			$insrt = Mage::getModel("awodev_aworewards/user");
			$insrt->setData('user_id',$user_id);
			$insrt->setData('url_real',$url_real);
			if(!empty($url_short)) $insrt->setData('url_short',$url_short);
			$insrt->save();
			
			//aworewardsh::query('INSERT INTO #__aworewards_user (user_id,url_real,url_short) vALUES ('.$user_id.',"'.self::escape($url_real).'",'.$url_short_str.')');
		}
		else {
			$return_url = $userrowdata['url_real'];
			if(!empty($is_url_short)) {
				if(empty($userrowdata['url_short'])) {
					$return_url = $url_real = Mage::getUrl('awodev_aworewards/register',array('id'=>urlencode($this->simple_number_encrypt((int)$user_id))));
					$oauth = Mage::helper('awodev_aworewards/oauth_google');
					$oauth->init();
					$url_short = $oauth->get_urlshort($url_real);
					if(!empty($url_short)) $return_url = $url_short;
					
					$userrow->setData('url_real',$url_real);
					if(!empty($url_short)) $userrow->setData('url_short',$url_short);
					$userrow->save();
					
					//$url_short_str = empty($url_short) ? 'NULL' : '"'.self::escape($url_short).'"';
					//aworewardsh::query('UPDATE #__aworewards_user SET url_real="'.self::escape($url_real).'",url_short='.$url_short_str.' WHERE user_id='.$user_id);
				}
				else $return_url = $userrowdata['url_short'];
			}
		}
		return $return_url;
	}
	

	public function getCustomerPointTotal($customer_id=null) {
		
		$points = array(
			'total' => 0,
			'unclaimed' => 0,
			'claimed' => 0,
		);
		
		if ($customer_id == null) $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
		$customer_id = (int)$customer_id;
		if(empty($customer_id)) return $points;
		
		
        $collection = Mage::getResourceModel('awodev_aworewards/credit_collection')
            ->addFieldToFilter('main_table.user_id', $customer_id)
            ->addFieldToFilter('main_table.credit_type','points')
        ;
		
		$collection->getSelect()
				->reset('columns')
				->columns(new Zend_Db_Expr('SUM(points) as total'))
				->columns(new Zend_Db_Expr('SUM(points_paid) as claimed'))
				->columns(new Zend_Db_Expr('SUM(points-IFNULL(points_paid,0)) AS unclaimed'))
				->group(new Zend_Db_Expr('main_table.user_id'))
		;
		$points = $collection->getFirstItem()->getData();	
		return $points;
	}
	
	public function getTable($table) { return Mage::getSingleton('core/resource')->getTableName($table); }
	
}

if (!function_exists('printr')) { function printr($a) { echo '<pre>'.print_r($a,1).'</pre>'; } }
if (!function_exists('printrx')) { function printrx($a) { echo '<pre>'.print_r($a,1).'</pre>'; exit; } }
