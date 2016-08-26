<?php

class AwoDev_AwoRewards_Helper_Reward_Registration extends AwoDev_AwoRewards_Helper_Reward {
 
	
	public function processRegistration($customer) {
	
		$current_date = date('Y-m-d H:i:s');
		@$customer_id = (int)$customer['entity_id'];
		if(empty($customer_id)) return;
		
		$this->customer = $this->get_customer($customer_id);
		if(empty($this->customer->id)) return;
		$this->customer;
		
		
		$this->processRegistrationCheck('everyone',null,$this->customer);
		
		$referral_id = Mage::getSingleton('customer/session')->getData('aworewards_referral_id');
		if(empty($referral_id)) @$referral_id = (int)$_COOKIE['aworewards_referral_id'];
		
		if(!empty($referral_id)) {
			$sponsor = $this->get_customer($referral_id);
			if(!empty($sponsor->id)) {
				Mage::getSingleton('customer/session')->setData('aworewards_referral_id',null);
				setcookie('aworewards_referral_id', "", time()-3600);
		
				$sponsor->rid = Mage::getModel('awodev_aworewards/referral')
							->getCollection()
							->addFieldToFilter('user_id',$sponsor->id)
							->addFieldToFilter('email',$this->customer->email)
							->getFirstItem()
							->getId()
				;
				if(empty($sponsor->rid)) {
					$newref = Mage::getModel('awodev_aworewards/referral');
					$newref->setData('user_id',$sponsor->id);
					$newref->setData('email',$this->customer->email);
					$newref->save();
					$sponsor->rid = $newref->getId();
				}
			}
		}

		if(empty($sponsor->id)) $sponsor = $this->get_sponsor('friend_email',$this->customer->email);
		if(empty($sponsor->id)) return;
		$this->sponsor = $sponsor;
		
		if(empty($newref)) $newref = Mage::getModel('awodev_aworewards/referral')->load($sponsor->rid);
		$newref->setData('join_user_id',$this->customer->id);
		$newref->setData('join_date',$this->customer->created_at);
		$newref->save();
		
				
		$this->processRegistrationCheck('sponsor',$this->sponsor,$this->customer);
		$this->processRegistrationCheck('friend',$this->sponsor,$this->customer);

	
		return true;
	}
	public function processRegistrationSys() {
			
			
		foreach (Mage::app()->getWebsites() as $website) {
			$website_id = $website->getData('website_id');
			$this->processRegistrationCheckSys($website_id,'everyone');
			$this->processRegistrationCheckSys($website_id,'sponsor');
			$this->processRegistrationCheckSys($website_id,'friend');
		}
	
		return true;
	}
	private   function processRegistrationCheck($_type,$sponsor,$customer) {
	
		if(empty($customer)) return;

		$current_date = date('Y-m-d H:i:s');
		
		$collection = Mage::getModel('awodev_aworewards/rule')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',$customer->website_id)
					->addFieldToFilter('rule_type','registration')
					->addFieldToFilter('customer_type',$_type)
		;

		$collection->getSelect()->where('(	 
							((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
							((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
							((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
							(startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
						)');
						
		$collection->setOrder('ordering', 'ASC');
		$collection->setOrder('id', 'ASC');
		$rule = (object)$collection->getFirstItem()->getDataFront($customer->store_id);

		if(empty($rule->id)) return;
		if($rule->customer_type=='everyone' && !empty($rule->params->reg_delay)) return;
		$this->credit($_type,$sponsor,$customer,$rule);
		
	}
	private   function processRegistrationCheckSys($website_id,$_type) {
	

		$current_date = date('Y-m-d H:i:s');
		
		$collection = Mage::getModel('awodev_aworewards/rule')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',$website_id)
					->addFieldToFilter('rule_type','registration')
					->addFieldToFilter('customer_type',$_type)
		;
		$collection->getSelect()->where('(	 
							((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
							((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
							((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
							(startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
						)');
						
		$collection->setOrder('ordering', 'ASC');
		$collection->setOrder('id', 'ASC');
		$rule = (object)$collection->getFirstItem()->getData();
		if(empty($rule->id)) return;

		if(!empty($rule->params)) $rule->params = (object) Mage::helper('core')->jsonDecode($rule->params);
		
		
		
		$collection = Mage::getModel("customer/customer")
					->getCollection()
		;
		//if($rule->customer_type=='sponsor') {
		//	$collection->getSelect()->join( array('r' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_referral')), 'r.user_id = e.entity_id', array('r.user_id AS sponsor_id'));
		//}
		//else
		if($rule->customer_type=='friend' || $rule->customer_type=='sponsor') {
			$collection->getSelect()->join( array('r' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_referral')), 'r.join_user_id = e.entity_id', array('r.user_id AS sponsor_id'));
		}
		$collection->getSelect()
					->joinLeft( array('c' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_credit')),
								'c.user_id=e.entity_id AND c.rule_type="'.$rule->rule_type.'" AND c.customer_type="'.$rule->customer_type.'"'
					)
					->joinLeft( array('o' => Mage::helper('awodev_aworewards')->getTable('sales_flat_order')),
								'o.customer_id=e.entity_id', 
								array(new Zend_Db_Expr('COUNT(o.entity_id) AS order_count'))
					)
					->group(new Zend_Db_Expr('e.entity_id'))
				;
		if(!empty($rule->startdate)) $collection->getSelect()->where('e.created_at>="'.$rule->startdate.'"');
		if(!empty($rule->expiration)) $collection->getSelect()->where('e.created_at<="'.$rule->expiration.'"');
		if(!empty($rule->params->reg_delay)) $collection->getSelect()->where('UNIX_TIMESTAMP() > UNIX_TIMESTAMP(e.created_at)+'.($rule->params->reg_delay*86400));
		if(!empty($rule->params->reg_nosend)) $collection->getSelect()->having('order_count=0');	
		 $collection->getSelect()->where('c.user_id IS NULL')->limit( 10 );
	
		foreach($collection as $item) {
			$each_friend = (object)$item->getData();
			$each_sponsor = null;
			if(!empty($each_friend->sponsor_id)) {
				$each_sponsor = $this->get_sponsor('friend_id',$each_friend->entity_id);
				if(empty($each_sponsor)) continue;
			}
			
			$each_friend = $this->get_customer($item->getData('entity_id'));
			$each_rule = (object) Mage::getModel('awodev_aworewards/rule')->getCollection()->addFieldToFilter('id',$rule->id)->getFirstItem()->getDataFront($each_friend->store_id);
			$this->credit($_type,$each_sponsor,$each_friend,$each_rule);
		}
		
		
	}



	
}

