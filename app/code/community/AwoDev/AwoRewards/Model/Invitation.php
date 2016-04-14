<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Invitation extends Mage_Core_Model_Abstract {
	protected function _construct() {  
		$this->_init('awodev_aworewards/invitation');
	} 
	
	public function fixforsave() {
		$data = $this->getData();
		if(empty($data['invitation'])) return;
		
		// set null fields
		if(empty($data['invitation']['coupon_template'])) $data['invitation']['coupon_template'] = $data['invitation']['coupon_expiration'] = null;
		if(empty($data['invitation']['coupon_expiration'])) $data['invitation']['coupon_expiration'] = null;

		
		//correct invalid data
		if($data['invitation']['invitation_type']=='email') {
		}
		elseif($data['invitation']['invitation_type']=='facebook') {
			$data['invitation']['email_subject'] = null;
		}
		elseif($data['invitation']['invitation_type']=='twitter') {
			$data['invitation']['email_subject'] = null;
		}


		if(empty($data['invitation']['params'])) $data['invitation']['params'] = null;
		else $data['invitation']['params'] = (object)$data['invitation']['params'];
		
		$this->setData($data);
	}
	
    public function validate() {
        $errors = array();
        $helper = Mage::helper('awodev_aworewards');
		
		$data_all = $this->getData();
		if(empty($data_all['invitation'])) {
			$errors[] = $helper->__('Invalid configuration');
			return $errors;
		}
		
		$data = (object)$data_all['invitation'];
		if(!empty($data->params)) $data->params = (object)$data->params;
		if(!empty($data->locale)) $data->locale = (object)$data->locale;
        
		
		
		$check_type = $helper->vars('invitation_type',$data->invitation_type);
		$check_published = $helper->vars('published',$data->published);

		if(empty($data->website_id) || !ctype_digit($data->website_id)) $errors[] = $helper->__('Website').': '.$helper->__('please enter a valid value');
		if(empty($data->invitation_name))  $errors[] = $helper->__('Title').': '.$helper->__('please enter a valid value');
		if(empty($check_published)) $errors[] = $helper->__('Published').': '.$helper->__('please enter a valid value');
		if(empty($check_type)) $errors[] = $helper->__('Form Type').': '.$helper->__('please enter a valid value');

		if($data->invitation_type=='email') {
			if(empty($data->locale->email_subject[0])) $errors[] = $helper->__('Email Subject').': '.$helper->__('please enter a valid value');
		}
		if(empty($data->locale->email_body[0])) $errors[] = $helper->__('Message').': '.$helper->__('please enter a valid value');

		if(!empty($data->coupon_template) && !ctype_digit($data->coupon_template)) $errors[] = $helper->__('Coupon to Copy').': '.$helper->__('please enter a valid value');
		if(!empty($data->coupon_expiration) && !ctype_digit($data->coupon_expiration)) $errors[] = $helper->__('Coupon Expiration').': '.$helper->__('please enter a valid value');
		
		return $errors;
	}
	
	public function save() {
		$data_all = $this->getData();
	
		if(empty($data_all['invitation'])) return;
		$data = $data_all['invitation'];
		$data['id'] = (int)$data['id'];
		$_isnew = empty($data['id']) ? true : false;
	
        $resource = Mage::getSingleton('core/resource');
		$dbread = $resource->getConnection('core_read');
		$dbwrite = $resource->getConnection('core_write');
		
		
		$prev_ordering = 0;
		if(!$_isnew) {
			$select = $dbread->select()
				->from($resource->getTableName('awodev_aworewards/invitation'), 'ordering')
				->where('id=?', $data['id']);
			$tmp = $dbread->fetchOne($select);
			//echo '<pre>'; print_r($tmp); exit;
			$prev_ordering = (int)$tmp;
		}
		
		
		if($_isnew) $this->setData('website_id', $data['website_id'] );
		else $this->load($data['id']);
		
		$this->setData('invitation_name', $data['invitation_name'] ); 
		$this->setData('invitation_type', $data['invitation_type'] ); 
		$this->setData('coupon_template', $data['coupon_template'] ); 
		$this->setData('coupon_expiration', $data['coupon_expiration'] ); 
		$this->setData('published', $data['published'] ); 
		$this->setData('params', !empty($data['params']) ? Mage::helper('core')->jsonEncode($data['params']) : null ); 
		$this->setData('ordering', 1 ); 

		parent::save();

		
		$id = (int)$this->getData('id');

		if(!empty($id)) {
			{ // fix ordering 
				$ordering = (int)$data['ordering'];
				if(!empty($prev_ordering) && $prev_ordering==$ordering) {
					$this->setData('ordering',$ordering);
					parent::save();
				}	
				elseif(empty($ordering)) {
					$select = $dbread->select()
						->from($resource->getTableName('awodev_aworewards/invitation'), array(new Zend_Db_Expr('max(ordering) as maxid')))
						->where('invitation_type=?', $this->getData('invitation_type'));
					$tmp = (int)$dbread->fetchOne($select);
					$this->setData('ordering',$tmp + 1);
					parent::save();
				}
				else {
					$select = $dbread->select()
						->from($resource->getTableName('awodev_aworewards/invitation'))
						->where('invitation_type=?', $this->getData('invitation_type'))
						->where('id!=?',$this->getData('id'));
					$listload = $dbread->fetchAll($select);
					
					//echo '<pre>'; print_r($listload);exit;
					$groupings = array();
					$_added_lonesome = false;
					foreach($listload as $item) {
						if(!$_added_lonesome && $ordering<=$item['ordering']) {
							$_added_lonesome = true;
							$groupings[] = $this->getData('id');
						}
						$groupings[] = $item['id'];
					}
					if(!$_added_lonesome) $groupings[] = $this->getData('id');
					
					foreach($groupings as $i=>$itemid) 
						$dbwrite->update($resource->getTableName('awodev_aworewards/invitation'), array('ordering'=>$i+1), 'id='.$itemid);

				}
			}
			
			// update language
			Mage::getModel('awodev_aworewards/locale')->setEntity('invitation',$id,$data['locale']);
			
		}
		
	
	}

    public function load($id, $field=null) {
		parent::load($id,$field);
		$locale = Mage::getModel('awodev_aworewards/locale')->getEntity('invitation',(int)$this->getData('id'));
		$this->setData('locale',$locale);
		
		return $this;
	}

    public function delete() {
		Mage::getModel('awodev_aworewards/locale')->deleteEntity('invitation',(int)$this->getData('id'));
		parent::delete();
	}
	
	public function getDataFront() {
		//$customer_id = Mage::getSingleton('customer/session')->getData();
		$store_id = Mage::app()->getStore()->getId();
		$local_ = array();
		$collections = Mage::getModel('awodev_aworewards/locale')->getCollection()
			->addFieldToFilter('entity', 'invitation')
			->addFieldToFilter('entity_id', $this->getData('id'))
			->addFieldToFilter('store_id', array('in'=>array(0,$store_id)))
		;
		
		$locale = array();
		foreach ($collections as $item) {
			$data = $item->getData();
			$col = $item->getData('col');
			if(!isset($locale[$data['col']])) $locale[$data['col']] = $data['value'];
			elseif($data['store_id']!=0) $locale[$data['col']] = $data['value'];
		}
		
		// add defaults
		$language_fields = array('description','email_body','email_subject');
		foreach($language_fields as $i) {
			if(!isset($locale[$i])) $locale[$i] = '';
		}
		
		// set data
		foreach($locale as $col=>$value) $this->setData($col,$value);
		return $this->getData();
	}
	
/*	public function getCollectionFront() {
		//$customer_id = Mage::getSingleton('customer/session')->getData();
		$store_id = Mage::app()->getStore()->getId();
		
		$local_ = array();
		$collections = Mage::getModel('awodev_aworewards/locale')->getCollection()
			->addFieldToFilter('entity', 'invitation')
			->addFieldToFilter('store_id', array('in'=>array(0,3)))
		;
		$locale = array();
		foreach ($collections as $item) {
			$data = $item->getData();
			$col = $item->getData('col');
			if(!isset($locale[$data['col']])) $locale[$data['col']] = $data['value'];
			elseif($data['store_id']!=0) $locale[$data['col']] = $data['value'];
		}
		
		
		
		
		echo '<pre>'; print_r($locale);exit;
		return $this->getCollection();
	}
*/
}