<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Rule extends Mage_Core_Model_Abstract {
    protected function _construct() {  
		$this->_init('awodev_aworewards/rule');
	} 
	
	public function fixforsave() {
		$data = $this->getData();
		if(empty($data['rule'])) return;
		
		// set null fields
		if(empty($data['rule']['startdate'])) $data['rule']['startdate'] = null;
		if(empty($data['rule']['expiration'])) $data['rule']['expiration'] = null;
		if(empty($data['rule']['template_id'])) $data['rule']['template_id'] = null;
		if(empty($data['rule']['points'])) $data['rule']['points'] = null;
		if(empty($data['rule']['note'])) $data['rule']['note'] = null;
		

		if($data['rule']['rule_type']=='registration') {
			if(empty($data['rule']['startdate'])) $data['rule']['startdate'] = date('Y-m-d H:i:s');
			if($data['rule']['credit_type']=='points') unset($data['rule']['params']['reg_delay']);

			unset(	$data['rule']['params']['order_min_type'],
					$data['rule']['params']['order_min'],
					$data['rule']['params']['order_trigger'],
					$data['rule']['params']['order_percent'],
					$data['rule']['params']['username']
			);
			$data['rule']['locale']['promo_headline'] = null;
			$data['rule']['locale']['promo_description'] = null;
			$data['rule']['locale']['predefined_text'] = null;
		}
		elseif($data['rule']['rule_type']=='review') {

			unset(	$data['rule']['params']['order_min_type'],
					$data['rule']['params']['order_min'],
					$data['rule']['params']['order_trigger'],
					$data['rule']['params']['order_percent'],
					$data['rule']['params']['username']
			);
			$data['rule']['locale']['promo_headline'] = null;
			$data['rule']['locale']['promo_description'] = null;
			$data['rule']['locale']['predefined_text'] = null;
			
		}
		elseif($data['rule']['rule_type']=='order') {
			unset(	$data['rule']['params']['reg_delay'],
					$data['rule']['params']['reg_nosend'],
					$data['rule']['params']['username']
			);
			$data['rule']['locale']['promo_headline'] = null;
			$data['rule']['locale']['promo_description'] = null;
			$data['rule']['locale']['predefined_text'] = null;
			
			if(empty($data['rule']['params']['order_min'])) unset($data['rule']['params']['order_min']);
			if(empty($data['rule']['params']['order_trigger'])) unset($data['rule']['params']['order_trigger']);
			if(empty($data['rule']['params']['order_percent'])) unset($data['rule']['params']['order_percent']);
		}
		elseif($data['rule']['rule_type']=='facebook_like') {
			$data['rule']['customer_type'] = 'everyone';
			unset(	$data['rule']['params']['reg_delay'],
					$data['rule']['params']['reg_nosend'],
					$data['rule']['params']['order_min_type'],
					$data['rule']['params']['order_min'],
					$data['rule']['params']['order_trigger'],
					$data['rule']['params']['order_percent']
			);
			$data['rule']['locale']['predefined_text'] = null;
			if(empty($data['rule']['locale']['promo_description'][0])) $data['rule']['locale']['promo_description'] = null;
		}
		elseif($data['rule']['rule_type']=='facebook_wall') {
			$data['rule']['customer_type'] = 'everyone';
			unset(	$data['rule']['params']['reg_delay'],
					$data['rule']['params']['reg_nosend'],
					$data['rule']['params']['order_min_type'],
					$data['rule']['params']['order_min'],
					$data['rule']['params']['order_trigger'],
					$data['rule']['params']['order_percent'],
					$data['rule']['params']['username']
			);
			if(empty($data['rule']['locale']['promo_description'][0])) $data['rule']['locale']['promo_description'] = null;
		}
		elseif($data['rule']['rule_type']=='twitter_tweet') {
			$data['rule']['customer_type'] = 'everyone';
			unset(	$data['rule']['params']['reg_delay'],
					$data['rule']['params']['reg_nosend'],
					$data['rule']['params']['order_min_type'],
					$data['rule']['params']['order_min'],
					$data['rule']['params']['order_trigger'],
					$data['rule']['params']['order_percent'],
					$data['rule']['params']['username']
			);
			if(empty($data['rule']['locale']['promo_description'][0])) $data['rule']['locale']['promo_description'] = null;
		}
		elseif($data['rule']['rule_type']=='twitter_follow') {
			$data['rule']['customer_type'] = 'everyone';
			unset(	$data['rule']['params']['reg_delay'],
					$data['rule']['params']['reg_nosend'],
					$data['rule']['params']['order_min_type'],
					$data['rule']['params']['order_min'],
					$data['rule']['params']['order_trigger'],
					$data['rule']['params']['order_percent']
			);
			$data['rule']['locale']['predefined_text'] = null;
			if(empty($data['rule']['locale']['promo_description'][0])) $data['rule']['locale']['promo_description'] = null;
		}

		if($data['rule']['credit_type']=='mage_coupon') {
			$data['rule']['points'] = null;
		}
		elseif($data['rule']['credit_type']=='points') {
			$data['rule']['template_id'] = null;
			unset($data['rule']['params']['coupon_expiration']);
			if($data['rule']['rule_type']=='order' && !empty($data['rule']['params']['order_percent'])) $data['rule']['points'] = null;
			$data['rule']['locale']['email_subject'] = null;
			$data['rule']['locale']['email_body'] = null;
		}
		
		
		
		

		if(empty($data['rule']['params'])) $data['rule']['params'] = null;
		else $data['rule']['params'] = (object)$data['rule']['params'];
		
		$this->setData($data);
	}
	
    public function validate() {
        $errors = array();
        $helper = Mage::helper('awodev_aworewards');
		
		$data_all = $this->getData();
		if(empty($data_all['rule'])) {
			$errors[] = $helper->__('Invalid configuration');
			return $errors;
		}
		
		$data = (object)$data_all['rule'];
		if(!empty($data->params)) $data->params = (object)$data->params;
		if(!empty($data->locale)) $data->locale = (object)$data->locale;
        

		
		$check_rule_type = $helper->vars('rule_type',$data->rule_type);
		$check_customer_type = $helper->vars('customer_type',$data->customer_type);
		$check_credit_type = $helper->vars('credit_type',$data->credit_type);
		
		if(empty($data->website_id) || !ctype_digit($data->website_id)) $errors[] = $helper->__('Website').': '.$helper->__('please enter a valid value');
		
		

		if(empty($data->rule_name)) $errors[] = $helper->__('Rule Name').': '.$helper->__('please enter a valid value');
		if(empty($data->published) || ($data->published!='1' && $data->published!='-1'))  $errors[] = $helper->__('Published').': '.$helper->__('please enter a valid value');
		if(empty($check_customer_type)) $errors[] = $helper->__('Customer type').': '.$helper->__('please enter a valid value');
	

		$is_start = true;
		if(!empty($data->startdate)) {
			if(!preg_match("/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/",$data->startdate)) {
				$is_start = false;
				$errors[] = $helper->__('Start Date').': '.$helper->__('please enter a valid value');
			}
			else {
				list($dtmp,$ttmp) = explode(' ',$data->startdate);
				list($Y,$M,$D) = explode('-',$dtmp);
				list($h,$m,$s) = explode(':',$ttmp);
				if($Y>2100 || $M>12 || $D>31 || $h>23 || $m>59 || $s>59) {
					$is_start = false;
					$errors[] = $helper->__('Start Date').': '.$helper->__('please enter a valid value');
				}
			}
		} else $is_start = false;
		$is_end = true;
		if(!empty($data->expiration)) {
			if(!preg_match("/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/",$data->expiration)) {
				$is_end = true;
				$errors[] = $helper->__('Expiration').': '.$helper->__('please enter a valid value');
			}
			else {
				list($dtmp,$ttmp) = explode(' ',$data->expiration);
				list($Y,$M,$D) = explode('-',$dtmp);
				list($h,$m,$s) = explode(':',$ttmp);
				if($Y>2100 || $M>12 || $D>31 || $h>23 || $m>59 || $s>59) {
					$is_end = true;
					$errors[] = $helper->__('Expiration').': '.$helper->__('please enter a valid value');
				}
			}
		} else $is_end = false;
		if($is_start && $is_end) {
			list($dtmp,$ttmp) = explode(' ',$data->startdate);
			list($Y,$M,$D) = explode('-',$dtmp);
			list($h,$m,$s) = explode(':',$ttmp);
			$c1 = (int)$Y.$M.$D.'.'.$h.$m.$s;
			list($dtmp,$ttmp) = explode(' ',$data->expiration);
			list($Y,$M,$D) = explode('-',$dtmp);
			list($h,$m,$s) = explode(':',$ttmp);
			$c2 = (int)$Y.$M.$D.'.'.$h.$m.$s;
			if($c1>$c2) $errors[] = $helper->__('Start Date/Expiration').': '.$helper->__('please enter a valid value');
		}




		
		
		if(empty($check_credit_type)) $errors[] = $helper->__('Credit type').': '.$helper->__('please enter a valid value');
		if($data->credit_type=='points') {
			if($data->rule_type=='order') {
				if(!empty($data->points) && (!is_numeric($data->points) || $data->points<0.01)) $errors[] = $helper->__('Points').': '.$helper->__('please enter a valid value');
				if(!empty($data->params->order_percent) && (!is_numeric($data->params->order_percent) || $data->params->order_percent<0.01))$errors[] = $helper->__('Percent of Order Total').': '.$helper->__('please enter a valid value');
				if(empty($data->points) && empty($data->params->order_percent)) $errors[] = $helper->__('Points').'/'.$helper->__('Percent of Order Total').': '.$helper->__('please enter a valid value');
			}
			else {
				if(!is_numeric($data->points) || $data->points<0.01) $errors[] = $helper->__('Points').': '.$helper->__('please enter a valid value');
			}
			
		}
		elseif($data->credit_type=='mage_coupon') {
			if(empty($data->template_id)) $errors[] = $helper->__('Coupon to Copy').': '.$helper->__('please enter a valid value');
			if(!empty($data->params->coupon_expiration) && !ctype_digit($data->params->coupon_expiration)) $errors[] = $helper->__('Coupon Expiration').': '.$helper->__('please enter a valid value');

			if(empty($data->locale->email_subject[0])) $errors[] = $helper->__('Email Subject').': '.$helper->__('please enter a valid value');
			if(empty($data->locale->email_body[0])) $errors[] = $helper->__('Message').': '.$helper->__('please enter a valid value');
		}
		
		
		
		if(empty($check_rule_type)) $errors[] = $helper->__('Rule type').': '.$helper->__('please enter a valid value');
		if($data->rule_type == 'registration') {
			if((!empty($data->params->reg_delay) && !ctype_digit($data->params->reg_delay))
			|| (empty($data->params->reg_delay) && !empty($data->params->reg_nosend))) $errors[] =  $helper->__('Delay in Days').': '.$helper->__('please enter a valid value');
		} 
		elseif($data->rule_type=='order') {
			if($data->params->order_min_type!='each' && $data->params->order_min_type!='all') $errors[] = $helper->__('Minimum Order Total Type').': '.$helper->__('please enter a valid value');
			
			if(!empty($data->params->order_min) && (!is_numeric($data->params->order_min) || $data->params->order_min<0.01) ) $errors[] = $helper->__('Minimum Order Total').': '.$helper->__('please enter a valid value');
			if(!empty($data->params->order_trigger)) {
				if(!preg_match("/^\d+(\s*,\s*\d+)*\s*$/",$data->params->order_trigger)) {
					$errors[] = $helper->__('Order Number to trigger rule').': '.$helper->__('please enter a valid value');
				}
			}
		}
		elseif($data->rule_type=='facebook_like') {
			if(empty($data->params->username)) $errors[] = $helper->__('Username').': '.$helper->__('please enter a valid value');

			if(empty($data->locale->promo_headline[0])) $errors[] = $helper->__('Marketing Headline').': '.$helper->__('please enter a valid value');
		}
		elseif($data->rule_type=='facebook_wall') {
			if(empty($data->locale->predefined_text[0])) $errors[] = $helper->__('Predefined Text').': '.$helper->__('please enter a valid value');
			if(empty($data->locale->promo_headline[0])) $errors[] = $helper->__('Marketing Headline').': '.$helper->__('please enter a valid value');
		}
		elseif($data->rule_type=='twitter_tweet') {
			if(empty($data->locale->predefined_text[0])) $errors[] = $helper->__('Predefined Text').': '.$helper->__('please enter a valid value');
			if(empty($data->locale->promo_headline[0])) $errors[] = $helper->__('Marketing Headline').': '.$helper->__('please enter a valid value');
		}
		elseif($data->rule_type=='twitter_follow') {
			if(empty($data->params->username)) $errors[] = $helper->__('Username').': '.$helper->__('please enter a valid value');
			if(empty($data->locale->promo_headline[0])) $errors[] = $helper->__('Marketing Headline').': '.$helper->__('please enter a valid value');
		}
		
		return $errors;
	}
	
	public function save() {
		$data_all = $this->getData();
		//$this->setData(array());
	//$x =  $this->_getResource();
	//echo '<pre>'; print_r($x);exit;
	
	
	//echo '<pre>'; print_r($data_all);exit;
	
		if(empty($data_all['rule'])) return;
		$data = $data_all['rule'];
		$data['id'] = (int)$data['id'];
		$_isnew = empty($data['id']) ? true : false;
	
        $resource = Mage::getSingleton('core/resource');
		$dbread = $resource->getConnection('core_read');
		$dbwrite = $resource->getConnection('core_write');
		
		
		$prev_ordering = 0;
		if(!$_isnew) {
		
			$select = $dbread->select()
				->from($resource->getTableName('awodev_aworewards/rule'), 'ordering')
				->where('id=?', $data['id']);
			$tmp = $dbread->fetchOne($select);
			//echo '<pre>'; print_r($tmp); exit;
			$prev_ordering = (int)$tmp;
		}
		
		
		if($_isnew) $this->setData('website_id', $data['website_id'] );
		else $this->load($data['id']);
		
		$this->setData('rule_name', $data['rule_name'] ); 
		$this->setData('rule_type', $data['rule_type'] ); 
		$this->setData('customer_type', $data['customer_type'] ); 
		$this->setData('credit_type', $data['credit_type'] ); 
		$this->setData('template_id', $data['template_id'] ); 
		$this->setData('points', $data['points'] ); 
		$this->setData('startdate', $data['startdate'] ); 
		$this->setData('expiration', $data['expiration'] ); 
		$this->setData('published', $data['published'] ); 
		$this->setData('note', $data['note'] ); 
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
						->from($resource->getTableName('awodev_aworewards/rule'), array(new Zend_Db_Expr('max(ordering) as maxid')))
						->where('rule_type=?', $this->getData('rule_type'));
					$tmp = (int)$dbread->fetchOne($select);
					$this->setData('ordering',$tmp + 1);
					parent::save();
				}
				else {
					$select = $dbread->select()
						->from($resource->getTableName('awodev_aworewards/rule'))
						->where('rule_type=?', $this->getData('rule_type'))
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
						$dbwrite->update($resource->getTableName('awodev_aworewards/rule'), array('ordering'=>$i+1), 'id='.$itemid);

				}
			}
			
			// update language
			Mage::getModel('awodev_aworewards/locale')->setEntity('rule',$id,$data['locale']);
			
		}
		
	
	}

    public function load($id, $field=null) {
		parent::load($id,$field);
		$locale = Mage::getModel('awodev_aworewards/locale')->getEntity('rule',(int)$this->getData('id'));
		$this->setData('locale',$locale);
		
		return $this;
	}

    public function delete() {
		Mage::getModel('awodev_aworewards/locale')->deleteEntity('rule',(int)$this->getData('id'));
		parent::delete();
	}
	
	
	public function getDataFront($store_id = null) {
		//$customer_id = Mage::getSingleton('customer/session')->getData();
		if(empty($store_id)) $store_id = Mage::app()->getStore()->getId();
		$collections = Mage::getModel('awodev_aworewards/locale')->getCollection()
			->addFieldToFilter('entity', 'rule')
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
		$language_fields = array('email_body','email_subject','predefined_text','promo_description','promo_headline');
		foreach($language_fields as $i) {
			if(!isset($locale[$i])) $locale[$i] = '';
		}
		
		// set data
		foreach($locale as $col=>$value) $this->setData($col,$value);
		
		
		// decode params
		$params_decoded = '';
		$params = $this->getData('params');
		if(!empty($params)) $params_decoded = (object) Mage::helper('core')->jsonDecode($params);
		$this->setData('params',$params_decoded);
		$this->setData('params_orig',$params);
		
		
		return $this->getData();
	}

	
}