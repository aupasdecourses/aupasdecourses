<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Reward_Social extends AwoDev_AwoRewards_Helper_Reward {
 
	
	public function processSocial($vars){

		@$type = $vars['type'];
		$allowed_types = array(
			'facebook_postpublic'=>'facebook_wall',
			'facebook_likeus'=>'facebook_like',
			'twitter_postpublic'=>'twitter_tweet',
			'twitter_likeus'=>'twitter_follow',
		);
		if(empty($allowed_types[$type])) return;
		
		@$user_id = (int)$vars['user_id'];
		@$rule_id = (int)$vars['rule_id'];
		
		$this->customer = $this->get_customer($user_id);
		if(empty($this->customer->id)) return;
		
		
		$current_date = date('Y-m-d H:i:s');
		$collection = Mage::getModel('awodev_aworewards/rule')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',$this->customer->website_id)
					->addFieldToFilter('rule_type',$allowed_types[$type])
					->addFieldToFilter('id',(int)$rule_id)
		;
		$collection->getSelect()->where('(	 
							((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
							((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
							((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
							(startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
						)');
						
		$collection->setOrder('ordering', 'ASC');
		$collection->setOrder('id', 'ASC');
		$rule = (object)$collection->getFirstItem()->getDataFront($this->customer->store_id);


		if(empty($rule->id)) return;
	
		$rule->item_details = new stdClass();
		$rule->item_details->item_id = @$vars['post_id'];

		$this->credit('everyone',null,$this->customer,$rule);		
	
		return true;
		
	}


	
}

