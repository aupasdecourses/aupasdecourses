<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Promotion extends Mage_Core_Block_Template {

	
	public function getFacebookAppID() {
		return Mage::getStoreConfig('awodev_aworewards/external_api/social_facebook_key');
	}
	
	public function is_js_facebook() {
		$socials = $this->getSocials();
		$include_js_facebook = false;
		if(empty($socials)) $include_js_facebook = false;
		else {
			foreach($socials as $social) {
				if($social->rule_type=='facebook_like') {
					$include_js_facebook = true;
					break;
				}
			}
		}
		if($include_js_facebook && !defined('AWOREWARDS_FB_JS_LOADED')) {
			define('AWOREWARDS_FB_JS_LOADED',1);
		}
		else $include_js_facebook = false;
		
		return $include_js_facebook;
		
	}
	
	public function getPromotions() {
		$rule_ids = array();
		$in_rule_ids = $this->getData('rule_ids');
		if(!empty($in_rule_ids)) {
			foreach(explode(',',$in_rule_ids) as $x) {
				$x = (int)$x;
				if(!empty($x)) $rule_ids[] = $x;
			}
		}
		
		
		$total_count = 0;
		$customer_id = (int)Mage::getSingleton('customer/session')->getCustomerId();		
				
		
		$is_facebook = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_facebook_enabled');
		$is_twitter = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_twitter_enabled');
		
		$socials = array();
		$is_title = true;
		$title = '';
		$include_js_facebook = false;

		if(empty($rule_ids)) {
			if($is_facebook) {
				$rule_id_fbwall = $this->getRuleActive(array('rule_type'=>array('facebook_wall')));
				if(!empty($rule_id_fbwall)) {
					reset($rule_id_fbwall);
					$socials[] = current($rule_id_fbwall);
				}
				$rule_id_fblike = $this->getRuleActive(array('rule_type'=>array('facebook_like')));
				if(!empty($rule_id_fblike)) {
					$include_js_facebook = true;
					reset($rule_id_fblike);
					$socials[] = current($rule_id_fblike);
				}
			}
			
			if($is_twitter) {
				$rule_id_tweet = $this->getRuleActive(array('rule_type'=>array('twitter_tweet')));
				if(!empty($rule_id_tweet)) {
					reset($rule_id_tweet);
					$socials[] = current($rule_id_tweet);
				}
				$rule_id_followus = $this->getRuleActive(array('rule_type'=>array('twitter_follow')));
				if(!empty($rule_id_followus)) {
					reset($rule_id_followus);
					$socials[] = current($rule_id_followus);
				}
			}
		
		}
		else {
			$ids = implode(',',$rule_ids);
			$rows = $this->getRuleActive(array('id'=>$rule_ids,'orderby_field'=>array('col'=>'id','values'=>implode(',',$rule_ids))));
			foreach($rows as $row) {
				switch($row->rule_type) {
					case 'facebook_wall':
					case 'facebook_like': {
						if($is_facebook) {
							$include_js_facebook = true;
							$socials[] = $row;
						}
						break;
					}
					case 'twitter_tweet':
					case 'twitter_follow': {
						if($is_twitter) $socials[] = $row;
						break;
					}
				}
			}
		}
		//$credited = aworewardsh::loadObjectList('SELECT rule_id FROM #__aworewards WHERE user_id='.(int)$customer_id.' AND rule_id>0','rule_id');
		$collection = Mage::getModel('awodev_aworewards/credit')
					->getCollection()
					->addFieldToFilter('user_id',$customer_id)
					->addFieldToFilter('rule_id',array('gt'=>0))
		;
		$credited = array();
		foreach($collection as $item) {
			$item = $item->getData('rule_id');
			$credited[$item] = $item;
		}
			

		foreach($socials as $k=>$social) {
			$socials[$k]->is_active = empty($credited[$social->id]) ? true : false;
			$socials[$k]->is_facebook_like = false;
			if(!empty($customer_id) && $social->rule_type=='facebook_like' && $socials[$k]->is_active) $socials[$k]->is_facebook_like = true;
			
			$socials[$k]->reward = '<img src="'.$this->getSkinUrl('images/awodev/aworewards/trophy.png').'" height="45" alt="reward" />';
			if($socials[$k]->credit_type == 'points') {
				$socials[$k]->reward =  round($socials[$k]->points);
			}
			
			if($socials[$k]->rule_type == 'facebook_wall') {
				$socials[$k]->hidden = array('getter'=>'facebook','getter_type'=>'postpublic',);
				$socials[$k]->image = 'promo_facebook.png';
			}
			elseif($socials[$k]->rule_type == 'facebook_like') {
				$socials[$k]->hidden = array('getter'=>'facebook','getter_type'=>'likeus',);
				$socials[$k]->image = 'promo_facebook_like.png';
			}
			elseif($socials[$k]->rule_type == 'twitter_tweet') {
				$socials[$k]->hidden = array('getter'=>'twitter','getter_type'=>'postpublic',);
				$socials[$k]->image = 'promo_tweet.png';
			}
			elseif($socials[$k]->rule_type == 'twitter_follow') {
				$socials[$k]->hidden = array('getter'=>'twitter','getter_type'=>'likeus',);
				$socials[$k]->image = 'promo_twitter.png';
			}
		}
		
		
		$this->setCredited($credited);
		$this->setSocials($socials);
		
		return $socials;
		
	}
	
	private function getRuleActive($args) {
	
		$collection = Mage::getModel('awodev_aworewards/rule')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',Mage::app()->getWebsite()->getId())
		;
		if(!empty($args['id'])) $collection->addFieldToFilter('id',array('in'=>$args['id']));
		if(!empty($args['rule_type'])) $collection->addFieldToFilter('rule_type',array('in'=>$args['rule_type']));

		$current_date = date('Y-m-d H:i:s');
		$collection->getSelect()->where('(	 
							((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
							((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
							((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
							(startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
						)');
		if(!empty($args['orderby_field'])) {
			$collection->getSelect()->order(new Zend_Db_Expr('FIELD('.$args['orderby_field']['col'].', ' .$args['orderby_field']['values'].')'));
		}
		else {
			$collection->setOrder('ordering', 'ASC');
			$collection->setOrder('id', 'ASC');
		}

		//$collection->load(true);

		$rows = array();
		foreach($collection as $item) {
			$rows[] = (object)$item->getDataFront();
		}

		return $rows;
	}
	


}