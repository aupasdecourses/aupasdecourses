<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Reward_Order extends AwoDev_AwoRewards_Helper_Reward {
 
	
	public function processOrder( $inorder ) {
		$this->sponsor = null;
		$this->order = (object)Mage::getModel('sales/order')->load($inorder['entity_id'])->getData();
		if(empty($this->order->entity_id)) return;
		$this->order->id = $this->order->entity_id;
		
		

		$this->customer = $this->get_customer($this->order->customer_id);
		if(empty($this->customer->id)) return;


		$collection = Mage::getModel('awodev_aworewards/credit')
					->getCollection()
					->addFieldToFilter('rule_type','order')
					->addFieldToFilter('item_id',$this->order->id)
		;
		// Mage::log('collection processorder reward_order OK',null,'awodev_1.log');
		// Mage::log(print_r($collection,1),null,'awodev_1.log');
		if($collection->getSize()>0) return; // prevent double points to the same rule
		
		
		$this->checkOrder('everyone');
		

		$this->sponsor = $this->get_sponsor('friend_id',$this->order->customer_id);
		if(empty($this->sponsor)) return;
				
		$this->checkOrder('sponsor');
		$this->checkOrder('friend');

		
		return true;
	
	}
	private function checkOrder($_type) {

		$current_date = date('Y-m-d H:i:s');	
		$website_id =  Mage::getModel('core/store')->load($this->order->store_id)->getWebsiteId();
		
		$collection = Mage::getModel('awodev_aworewards/rule')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',$website_id)
					->addFieldToFilter('rule_type','order')
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
		//Mage::log('checkOrder function - rule collections - OK: ',null,'awodev_1.log');
		$rules = array();
		foreach($collection as $item) $rules[] = (object) $item->getDataFront($this->order->store_id);
		// Mage::log(print_r($rules,1),null,'awodev_1.log');
		// Mage::log('Store Id: ',null,'awodev_1.log');
		// Mage::log(print_r($this->order->store_id,1),null,'awodev_1.log');
		if(empty($rules)) return;


		
		$passed_rule = array();
		foreach($rules as $rule) {
			if(empty($rule->params->order_min_type)) continue;
			
			$coupon_order_number = array();
			if(!empty($rule->params->order_trigger)) {
				foreach(explode(',',$rule->params->order_trigger) as $tmp2) {
					$tmp2 = (int)$tmp2;
					if(!empty($tmp2)) $coupon_order_number[] = $tmp2;
				}
			}

			// Mage::log('checkOrder function - coupon order number: ',null,'awodev_1.log');
			// Mage::log($coupon_order_number,null,'awodev_1.log');

			
			$order_min = !empty($rule->params->order_min) ? (float)$rule->params->order_min : 0;

			$row = null;
			if($rule->params->order_min_type=='each') {
				if(!empty($coupon_order_number)) {
					/*SELECT MAX(entity_id) AS `order_id`, COUNT(entity_id) AS `order_count` 
					FROM `sales_flat_order` AS `main_table`
					 WHERE (main_table.customer_id = '59')
					 AND (main_table.entity_id <= '16')
					 AND (base_grand_total >= 0)
					 AND ((base_total_due = 0) OR (main_table.entity_id = '16'))
					 AND (created_at >= '2014-01-01 00:00:00')
					 AND (created_at <= '2014-12-31 23:59:59')
					 GROUP BY `customer_id`
					 HAVING (order_count IN (16))*/							
					$collection = Mage::getModel('sales/order')
						->getCollection()
						->addFieldToFilter('customer_id',$this->order->customer_id)
						->addFieldToFilter('entity_id',array('lteq'=>$this->order->id))
						->addFieldToFilter('base_grand_total',array('gteq'=>$order_min))
						->addFieldToFilter(
								array('base_total_due', 'entity_id'),
								array(array('eq'=>0),array('eq'=>$this->order->id))
							)
					;
					// Mage::log('checkOrder function - collection customer id: ',null,'awodev_1.log');
					// Mage::log(print_r($this->order->customer_id,1),null,'awodev_1.log');
					if(!empty($rule->startdate)) $collection->addFieldToFilter('created_at',array('gteq'=>$rule->startdate));
					if(!empty($rule->expiration)) $collection->addFieldToFilter('created_at',array('lteq'=>$rule->expiration));
					$collection->getSelect()
						->reset(Zend_Db_Select::COLUMNS)
						->columns('MAX(entity_id) as order_id')
						->columns('COUNT(entity_id) as order_count')
						->group(array('customer_id'));	
					$collection->getSelect()->having('order_count IN ('.implode(',',$coupon_order_number).')');
					$rtn_order_id = $collection->getFirstItem()->getData('order_id');
					if($rtn_order_id != $this->order->id) continue;
				}	

				/*SELECT `main_table`.`base_grand_total` AS `order_total`, `main_table`.`created_at` AS `order_date`, `main_table`.`increment_id` AS `order_number`
				FROM `sales_flat_order` AS `main_table`
				LEFT JOIN `awodev_aworewards_credit` AS `c` ON c.item_id=main_table.entity_id AND c.rule_type="order" AND c.customer_type="everyone"
				WHERE (c.item_id IS NULL)
				AND (base_grand_total >= 0)
				AND (main_table.entity_id = '16')
				AND (created_at >= '2014-01-01 00:00:00')
				AND (created_at <= '2014-12-31 23:59:59')
				GROUP BY `entity_id`*/
				
				
				$collection = Mage::getModel('sales/order')
					->getCollection()
					->addFieldToFilter('c.item_id',array("null"=>true))
					->addFieldToFilter('base_grand_total',array('gteq'=>$order_min))
					->addFieldToFilter('entity_id',$this->order->id)
				;
				if(!empty($rule->startdate)) $collection->addFieldToFilter('created_at',array('gteq'=>$rule->startdate));
				if(!empty($rule->expiration)) $collection->addFieldToFilter('created_at',array('lteq'=>$rule->expiration));
				$collection->getSelect()
					->joinLeft( array('c' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_credit')),
								'c.item_id=main_table.entity_id AND c.rule_type="'.$rule->rule_type.'" AND c.customer_type="'.$rule->customer_type.'"')
					->reset(Zend_Db_Select::COLUMNS)
					->columns('grand_total as order_total')
					->columns('order_currency_code AS currency_code')
					->columns('created_at as order_date')
					->columns('increment_id as order_number')
					->group(array('entity_id'))
				;	
				$row = (object)$collection->getFirstItem()->getData();
			}
			elseif($rule->params->order_min_type=='all') {

				/*SELECT SUM(base_grand_total) AS `order_total`
				FROM `sales_flat_order` AS `main_table`
				WHERE (main_table.customer_id = '59')
				AND ((base_total_due = 0) OR (main_table.entity_id = '16'))
				AND (created_at >= '2014-01-01 00:00:00')
				AND (created_at <= '2014-12-31 23:59:59')
				GROUP BY `customer_id`
				HAVING (order_total>=500)
				LIMIT 10*/
				
				
				$collection = Mage::getModel('sales/order')
					->getCollection()
					->addFieldToFilter('customer_id',$this->order->customer_id)
					->addFieldToFilter(
							array('base_total_due', 'entity_id'),
							array(array('eq'=>0),array('eq'=>$this->order->id))
						)
				;
				if(!empty($rule->startdate)) $collection->addFieldToFilter('created_at',array('gteq'=>$rule->startdate));
				if(!empty($rule->expiration)) $collection->addFieldToFilter('created_at',array('lteq'=>$rule->expiration));
				$collection->getSelect()
					->reset(Zend_Db_Select::COLUMNS)
					->columns('SUM(base_grand_total) as order_total')
					->group(array('customer_id'))
					->having('order_total>='.$order_min)
					->limit(10)
				;
				$row = (object)$collection->getFirstItem()->getData();
				if(!empty($row->order_total)) {
					$row->order_number = '';
					$row->order_date = '';
					$row->currency_code = Mage::app()->getStore()->getBaseCurrencyCode();
				}
			}
			
			if(empty($row->order_total)) continue;
//printrx($row);
//printrx($this->order);
		
			if($rule->params->order_min_type=='all') {
				$collection = Mage::getModel('awodev_aworewards/credit')
							->getCollection()
							->addFieldToFilter('user_id',(int)($_type=='sponsor' ? $this->sponsor->id : $this->customer->id))
							->addFieldToFilter('rule_id',$rule->id)
				;
				if($collection->getSize()>0) return; // prevent double points to the same rule
			}

			$rule->order_details = $row;
			
			
			$passed_rule = $rule;
			break;
		}		

		
		if(empty($passed_rule)) return;
		$rule = $passed_rule;		
				
		$this->credit($_type,$this->sponsor,$this->customer,$rule);
		
	}
	




	
}

