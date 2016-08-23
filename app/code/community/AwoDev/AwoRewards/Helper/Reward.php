<?php

class AwoDev_AwoRewards_Helper_Reward extends Mage_Core_Helper_Abstract {
 
	
	protected function credit($_type,$sponsor,$customer,$rule,$order=null) {
		
		$credit_user = $_type=='sponsor' ? $sponsor : $customer;
		$referral_id = !empty($sponsor->rid) ? $sponsor->rid : NULL;
		
		
		if($rule->rule_type!='order' && $rule->rule_type!='review') {
		
			if($_type=='sponsor') {
				$collection = Mage::getModel('awodev_aworewards/credit')
							->getCollection()
							->addFieldToFilter('rule_id',$rule->id)
							->addFieldToFilter('user_id',$credit_user->id)
							->addFieldToFilter('referral_id',$referral_id)
				;
			}
			else {
				$collection = Mage::getModel('awodev_aworewards/credit')
							->getCollection()
							->addFieldToFilter('rule_id',$rule->id)
							->addFieldToFilter('user_id',$credit_user->id)
				;
			}
			if($collection->getSize()>0) return; // prevent double points to the same rule
		}
		
		
		if($rule->credit_type == 'points') {
			$points = $rule->points;
			$item_id = !empty($rule->item_details->item_id) ? $rule->item_details->item_id : NULL;
			if($rule->rule_type=='order') {
				if(!empty($rule->params->order_percent)) {
					$points = $this->order->base_grand_total * $rule->params->order_percent/100;
				}
				$item_id = $this->order->id;
			}
			if(empty($points)) return;
			
			$newcredit = Mage::getModel('awodev_aworewards/credit');
			$newcredit->setData('user_id',$credit_user->id);
			$newcredit->setData('referral_id',$referral_id);
			$newcredit->setData('rule_id',$rule->id);
			$newcredit->setData('rule_type',$rule->rule_type);
			$newcredit->setData('customer_type',$rule->customer_type);
			$newcredit->setData('credit_type',$rule->credit_type);
			$newcredit->setData('coupon_id',null);
			$newcredit->setData('item_id',$item_id);
			$newcredit->setData('points',$points);
			$newcredit->save();//save new rule
						
			$sponsor_point_notificastion = (int) Mage::getStoreConfig('awodev_aworewards/sponsor/mail_points_enabled');
			if($_type=='sponsor' && !empty($sponsor_point_notificastion)) {
				$FromName = Mage::getStoreConfig('awodev_aworewards/general/email_from_name');
				$MailFrom = Mage::getStoreConfig('awodev_aworewards/general/email_from_email');
				if(empty($FromName)) $FromName = Mage::getStoreConfig('trans_email/ident_general/name'); 
				if(empty($MailFrom)) $MailFrom = Mage::getStoreConfig('trans_email/ident_general/email');
				
				$collection = Mage::getResourceModel('awodev_aworewards/credit_collection')
						->addFieldToFilter('main_table.user_id', $credit_user->id)
						->addFieldToFilter('main_table.credit_type','points')
				;
				$collection->getSelect()
						->reset('columns')
						->columns(new Zend_Db_Expr('SUM(points) as total'))
						->columns(new Zend_Db_Expr('SUM(points_paid) as claimed'))
						->columns(new Zend_Db_Expr('SUM(points-IFNULL(points_paid,0)) AS unclaimed'))
						->group(new Zend_Db_Expr('main_table.user_id'))
				;
				$user_dbpoints = $collection->getFirstItem()->getData();	

				$postvars = new Varien_Object();
				$postvars->setData(array(
						'user_firstname'=>$credit_user->firstname,
						'user_lastname'=>$credit_user->lastname,
						'friend_firstname'=>$customer->firstname,
						'friend_lastname'=>$customer->lastname,
						'points_earned'=>round($points,2),
						'points_total'=>round($user_dbpoints['total'],2),
						'points_total_claimed'=>round($user_dbpoints['claimed'],2),
						'points_total_unclaimed'=>round($user_dbpoints['unclaimed'],2),
				));

				$mailer = Mage::getModel('core/email_template');
				$mailer->setDesignConfig(array('area' => 'frontend'))
						->setReplyTo($MailFrom)
						->setTemplateSubject(Mage::getStoreConfig('awodev_aworewards/sponsor/mail_points_email_subject'))
						->sendTransactional(
							Mage::getStoreConfig('awodev_aworewards/sponsor/mail_points_email_template'),
							array('name' => $FromName, 'email' => $MailFrom),
							$credit_user->email,
							$credit_user->firstname.' '.$credit_user->lastname,
							array('data'=>$postvars)
						);
				if (!$mailer->getSentSuccess());

			}
			
			
			Mage::helper('awodev_aworewards/payment')->coupon('automatic',$credit_user->id);
		}
		elseif($rule->credit_type == 'mage_coupon') {
			if(empty($rule->template_id)) return;
			
			
			if(empty($rule->email_body)) return;
			
			if(!Zend_Validate::is($credit_user->email, 'EmailAddress')) return;

			$obj = Mage::helper('awodev_aworewards/coupon')->generateCoupon($rule->template_id,null,!empty($rule->params->coupon_expiration) ? $rule->params->coupon_expiration : null);
			if(empty($obj->coupon_code)) return;

			// fix coupon
			$coupon_row = new stdclass;
			$coupon_row->id = $obj->coupon_id;
			$coupon_row->coupon_code = $obj->coupon_code;
			$coupon_row->coupon_value = $obj->rule->getData('discount_amount');
			$coupon_row->coupon_value_type =  $obj->rule->getData('simple_action')=='by_percent' ? 'percent' : 'amount';
			$coupon_row->expiration = $obj->expiration;


			$replace = null;
			if($rule->rule_type=='order') {
				if(!empty($rule->params->order_percent)) {
					$points = $this->order->base_grand_total * $rule->params->order_percent/100;
					if(empty($points)) {
						Mage::helper('awodev_aworewards/coupon')->deleteCoupon($obj->coupon_id);
						return;
					}
					
					if($obj->rule->getData('simple_action')=='by_percent')  $obj->rule->setData('simple_action','cart_fixed');
					$obj->rule->setData('discount_amount',$points);
					$obj->rule->save();
						
					$coupon_row->coupon_value = $points;
					$coupon_row->coupon_value_type = 'amount';
				}

					
				
				$order_date = '';
				if(!empty($rule->order_details->order_date))
					$order_date = Mage::helper('core')->formatDate($rule->order_details->order_date, 'medium', false);
				//$order_total_display = Mage::app()->getStore($this->order->store_id)->formatPrice($rule->order_details->order_total,true, false);
				$order_total_display = Mage::getModel('directory/currency')->load($rule->order_details->currency_code)->format($rule->order_details->order_total, array(), false);
				$order_total_display = $this->currency_htmlencode($order_total_display);
				$replace = array(
					'find'=>array('{order_number}','{order_total}','{order_date}',),
					'replace'=>array($rule->order_details->order_number,$order_total_display,$order_date,),
				);
			}			
			



			
			// update rewards tables
			
			$amount = $coupon_row->coupon_value_type=='amount' && !empty($coupon_row->coupon_value) ? $coupon_row->coupon_value : null;
			$item_id = !empty($rule->item_details->item_id) ? $rule->item_details->item_id : null;
			if($rule->rule_type=='order') $item_id = $this->order->id;

			$newcredit = Mage::getModel('awodev_aworewards/credit');
			$newcredit->setData('user_id',$credit_user->id);
			$newcredit->setData('referral_id',$referral_id);
			$newcredit->setData('rule_id',$rule->id);
			$newcredit->setData('rule_type',$rule->rule_type);
			$newcredit->setData('customer_type',$rule->customer_type);
			$newcredit->setData('credit_type',$rule->credit_type);
			$newcredit->setData('coupon_id',$coupon_row->id);
			$newcredit->setData('item_id',$item_id);
			$newcredit->setData('points',$amount);
			$newcredit->save();

			$newpayment = Mage::getModel('awodev_aworewards/payment');
			$newpayment->setData('payment_type','mage_coupon');
			$newpayment->setData('user_id',$credit_user->id);
			$newpayment->setData('coupon_id',$coupon_row->id);
			$newpayment->setData('amount_paid',$amount);
			$newpayment->setData('payment_details',$coupon_row->coupon_code);
			$newpayment->save();
			
			$newcredit->setData('payment_id',$newpayment->getData('id'));
			$newcredit->setData('points_paid',$amount);
			$newcredit->save();
							
			
			// send email
			$this->SendCouponEmail($rule,$_type,$coupon_row,$sponsor,$customer,$replace);
			
		}
	}

	
	protected function SendCouponEmail($rule,$_type,$coupon_row,$sponsor,$customer=null,$replace=null) {
	
		$credit_user = $_type=='sponsor' ? $sponsor : $customer;
		if(empty($credit_user->id)) return;
				
		// vendor info
		$FromName = Mage::getStoreConfig('awodev_aworewards/general/email_from_name');
		$MailFrom = Mage::getStoreConfig('awodev_aworewards/general/email_from_email');
		if(empty($FromName)) $FromName = Mage::getStoreConfig('trans_email/ident_general/name'); 
		if(empty($MailFrom)) $MailFrom = Mage::getStoreConfig('trans_email/ident_general/email');

		$coupon_row->coupon_price = '';
		if(!empty($coupon_row->coupon_value)) {
			if($coupon_row->coupon_value_type=='amount') {
				// displays current customer currency, converting coupon value from default currency
				//$coupon_row->coupon_price = Mage::helper('core')->currencyByStore($coupon_row->coupon_value, $credit_user->store_id,true, false);
				// base total formatted in base currency
				$coupon_row->coupon_price = Mage::app()->getStore()->getBaseCurrency()->format($coupon_row->coupon_value, array(), false);
				$coupon_row->coupon_price = $this->currency_htmlencode($coupon_row->coupon_price);

			}
			else $coupon_row->coupon_price = round($coupon_row->coupon_value).'%';
		}
		
		
		$store_name = Mage::getModel('core/store_group')->load($credit_user->group_id)->getName();
		$siteurl = Mage::helper('awodev_aworewards')->storeurl($credit_user->id);
		$today_date = Mage::helper('core')->formatDate();
	
		$sponsor_firstname = !empty($sponsor->firstname) ? $sponsor->firstname : '';
		$sponsor_lastname = !empty($sponsor->lastname) ? $sponsor->lastname : '';
	
		$dynamic_tags = array(
			'find'=>array('{siteurl}', '{store_name}', '{sponsor_firstname}', '{sponsor_lastname}', '{recipient_firstname}', '{recipient_lastname}', '{today_date}', ),
			'replace'=>array($siteurl, $store_name, $sponsor_firstname, $sponsor_lastname, $customer->firstname, $customer->lastname, $today_date,),
		);
		
		if(!empty($replace['find']) && !empty($replace['replace'])) {
			$dynamic_tags['find'] = array_merge($dynamic_tags['find'],$replace['find']);
			$dynamic_tags['replace'] = array_merge($dynamic_tags['replace'],$replace['replace']);
		}
		if(!empty($coupon_row->id)) {
			array_push($dynamic_tags['find'],'{coupon_code}','{coupon_value}','{coupon_expiration}');
			array_push($dynamic_tags['replace'],$coupon_row->coupon_code,$coupon_row->coupon_price,empty($coupon_row->expiration) ? '' : Mage::helper('core')->formatDate($coupon_row->expiration));
		}

		
		
				
		// message info
		$subject = $rule->email_subject;
		$message = $rule->email_body;
		
		$message = str_replace(	$dynamic_tags['find'],$dynamic_tags['replace'],$message );
		
		$mailer = Mage::getModel('core/email')
					//->setToName('')
					->setToEmail($credit_user->email)
					->setBody($message)
					->setSubject($subject)
					->setFromEmail($MailFrom)
					->setFromName($FromName)
					->setType('html')// YOu can use Html or text as Mail format
		;
		try {
			$mailer->send();
		}
		catch (Exception $e) {
			//exit( $e->getMessage());
			//Mage::helper('awodev_aworewards/coupon')->deleteCoupon($coupon_row->id);
			return false;
		}
		

		return true;
		
	}
	
	
	protected function get_customer($user_id) {
		
		$customer = (object) Mage::getModel("customer/customer")->load($user_id)->getData();
		$customer->id = !empty($customer->entity_id) ? (int)$customer->entity_id : 0;
		$customer->is_guest = empty($customer->id) ? true : false;
		return $customer;
		
	}
	protected function get_sponsor($type,$user_id) {
		$column = '';
		if($type=='friend_id') $column = 'join_user_id';
		elseif($type=='friend_email') $column = 'email';
		if(empty($column)) return null;
		
		$referral = (object) Mage::getResourceModel('awodev_aworewards/referral_collection')->addFieldToFilter($column,$user_id)->getFirstItem()->getData();
		//exit;
		if(empty($referral->id)) return null;
		$sponsor = (object) Mage::getModel('customer/customer')->load($referral->user_id)->getData();
		if(empty($sponsor->entity_id)) return null;
		
		$sponsor->id = $sponsor->entity_id;
		$sponsor->rid = $referral->id;
		return $sponsor;
	}




	private function currency_htmlencode($currency) {
		return htmlentities($currency, ENT_NOQUOTES | ENT_IGNORE, "UTF-8", true);
	}

	
}

