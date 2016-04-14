<?php

class AwoDev_AwoRewards_Helper_Reward_Review extends AwoDev_AwoRewards_Helper_Reward {
 


	public function processReviewSys(){
		
		foreach (Mage::app()->getWebsites() as $website) {
			$website_id = $website->getData('website_id');
			$this->processReviewCheck($website,'everyone');
			$this->processReviewCheck($website,'sponsor');
			$this->processReviewCheck($website,'friend');
		}
	
		return true;
	}
	private   function processReviewCheck($website,$_type) {
	
		$website_id = $website->getData('website_id');
			
			
		
		$current_date = date('Y-m-d H:i:s');
		$collection = Mage::getModel('awodev_aworewards/rule')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',$website_id)
					->addFieldToFilter('rule_type','review')
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
		
		/*
		SELECT `main_table`.*, `detail`.`detail_id`, `detail`.`title`, `detail`.`detail`, `detail`.`nickname`, `detail`.`customer_id`, `product`.`sku`
		FROM `review` AS `main_table`
		INNER JOIN `review_detail` AS `detail` ON main_table.review_id = detail.review_id
		INNER JOIN `review_store` AS `store` ON main_table.review_id=store.review_id
		INNER JOIN `catalog_product_entity` AS `product` ON main_table.entity_pk_value=product.entity_id
		INNER JOIN `customer_entity` AS `customer` ON detail.customer_id=customer.entity_id 
		LEFT JOIN `awodev_aworewards_credit` AS `credit` ON main_table.entity_id=credit.item_id 
		LEFT JOIN `awodev_aworewards_credit` AS `credit2` ON detail.customer_id=credit2.user_id 
		WHERE (store.store_id IN('1', '3', '2', '59745', '4', '59747'))
		AND (credit.item_id IS NULL AND credit.user_id IS NULL) 
		AND (main_table.created_at>="2000-01-01 00:00:00")
		AND (main_table.created_at<="2014-12-31 23:59:59") 
		AND (credit2.item_id IS NULL )
		AND (main_table.status_id=1)
		GROUP BY `main_table`.`review_id`
		LIMIT 10
		*/


		$store_ids = array();
		foreach ($website->getGroups() as $group) {
			$stores = $group->getStores();
			foreach ($stores as $store) {
				$store_ids[] = $store->getData('store_id');
			}
		}


		$collection = Mage::getModel('review/review')
					->getResourceCollection()
					->addStoreFilter( $store_ids )
					->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
					
		;  		
		$collection
			->getSelect()
			->join(array('product'=>Mage::helper('awodev_aworewards')->getTable('catalog_product_entity')),'main_table.entity_pk_value=product.entity_id',array('product.sku'))
			->join(array('customer'=>Mage::helper('awodev_aworewards')->getTable('customer_entity')),'detail.customer_id=customer.entity_id',array())
			->joinLeft(array('credit'=>Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_credit')),'main_table.review_id=credit.item_id AND credit.rule_type="review" AND credit.customer_type="'.$_type.'"',array())
			->joinLeft(array('credit2'=>Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_credit')),'detail.customer_id=credit2.user_id AND credit2.rule_type="review" AND credit2.customer_type="'.$_type.'"',array())
			->where('credit.item_id IS NULL AND credit.user_id IS NULL')
			->limit( 10 )
		;
		//if($rule->customer_type=='sponsor') {
		//	$collection->getSelect()->join( array('r' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_referral')), 'r.user_id = detail.customer_id', array('r.user_id AS sponsor_id'));
		//}
		//else
		if($rule->customer_type=='friend' || $rule->customer_type=='sponsor') {
			$collection->getSelect()->join( array('r' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_referral')), 'r.join_user_id = detail.customer_id', array('r.user_id AS sponsor_id'));
		}
		if(!empty($rule->startdate)) $collection->getSelect()->where('main_table.created_at>="'.$rule->startdate.'"');
		if(!empty($rule->expiration)) $collection->getSelect()->where('main_table.created_at<="'.$rule->expiration.'"');
		if(!empty($rule->params->review_onlyfirst)) $collection->getSelect()->where('credit2.item_id IS NULL ')->group(array('detail.customer_id'));
		else $collection->getSelect()->group(array('main_table.review_id'));

//$collection->load(true);
		$rows = array();
		foreach($collection as $item) $rows[] = (object) $item->getData();




		foreach($rows as $row) {

			$each_friend = $this->get_customer($row->customer_id);
			$each_sponsor = null;
			if(empty($each_friend->id)) continue;
			
			if(!empty($row->sponsor_id)) {
				$each_sponsor = $this->get_sponsor('friend_id',$row->customer_id);
				if(empty($each_sponsor)) continue;
			}

			
			$each_rule = (object) Mage::getModel('awodev_aworewards/rule')->getCollection()->addFieldToFilter('id',$rule->id)->getFirstItem()->getDataFront($each_friend->store_id);
			$row->item_id = $row->review_id;
			$each_rule->item_details = $row;
			$this->credit($_type,$each_sponsor,$each_friend,$each_rule);
		}

		return true;
		
	}
 

	
}

