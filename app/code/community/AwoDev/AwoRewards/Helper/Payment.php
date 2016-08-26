<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Payment extends Mage_Core_Helper_Abstract {
 
	
	public function paypal($receiverEmail,$user_id=null) {
		if ($user_id == null) $user_id = Mage::getSingleton('customer/session')->getCustomerId();
		$user_id = (int)$user_id;
		if($user_id<=0) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Customer not recognized'));
		}
	
		if(!Zend_Validate::is($receiverEmail, 'EmailAddress')) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Invalid email address'));
		}

		$customer = (object)Mage::getModel("customer/customer")->load($user_id)->getData();
		if(empty($customer->entity_id)) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
		}
	
		if(!Mage::getStoreConfig('awodev_aworewards/payout_paypal/enabled'))  {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Payment not allowed'));
		}
		
		
		$minimum_payout = Mage::getStoreConfig('awodev_aworewards/payout_paypal/minimum');
		$point_ratio = (float)Mage::getStoreConfig('awodev_aworewards/payout_paypal/point_ratio');
		if($point_ratio<=0) $point_ratio = 1;
		$error = '';
		
		
		
		
        $collection = Mage::getResourceModel('awodev_aworewards/credit_collection')
            ->addFieldToFilter('main_table.user_id', $customer->entity_id)
            ->addFieldToFilter('main_table.credit_type','points')
            ->addFieldToFilter('main_table.points',array('neq'=>new Zend_Db_Expr('IFNULL(main_table.points_paid,0)')))
        ;
		
		$collection->getSelect()
				->reset('columns')
				->columns('main_table.id')
				->columns(new Zend_Db_Expr('main_table.points-IFNULL(main_table.points_paid,0) AS balance'))
		;
		$balance = 0;
		$history_rows = array();
		foreach($collection as $item) {
			$row = (object)$item->getData();
			$balance += round($row->balance,2);
			$history_rows[$row->id] = $row;	
		}
		
		
		$balance_amount = round($balance/$point_ratio,2);
		
		if(empty($balance_amount) || $balance_amount<$minimum_payout) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Minimum payout amount not reached'));
		}
		
	
//$balance_amount=0.25;

		{ // make paypal payment
			$logger = Mage::helper('awodev_aworewards/payment_paypal_log');
			
			$env_prefix = Mage::getStoreConfig('awodev_aworewards/payout_paypal/sandbox') ? 'sandbox' : 'paypal';

			//$receiver = new stdClass;
			$receiver = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
			$receiver->initvars(array(
						'email'=>$receiverEmail,
						'amount'=>$balance_amount,
						'primary'=>'false',
						'phone'=>null,
						'invoiceId'=>null,
						'paymentType'=>null,
						'paymentSubType'=>null,
					));
			
			$receiverList = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
			$receiverList->initvars(array('receiver'=>array(clone($receiver)),));
			
			$requestEnvelope = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
			$requestEnvelope->initvars(array('detailLevel'=>null, 'errorLanguage'=>'en_US',));

			//$payRequest = new stdClass;
			$payRequest = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
			$payRequest->initvars(array(
						'requestEnvelope'=>clone($requestEnvelope),
						'clientDetails'=>null,
						'actionType'=>'PAY',
						'cancelUrl'=>Mage::helper('awodev_aworewards')->storeurl(),
						'currencyCode'=>Mage::getStoreConfig('awodev_aworewards/payout_paypal/currency'),
						'feesPayer'=>Mage::getStoreConfig('awodev_aworewards/payout_paypal/feepayer'),
						'ipnNotificationUrl'=>null,
						'memo'=>Mage::getStoreConfig('awodev_aworewards/payout_paypal/memo'),
						'pin'=>null,
						'preapprovalKey'=>null,
						'receiverList'=>clone($receiverList),
						'reverseAllParallelPaymentsOnError'=>null,
						'returnUrl'=>Mage::helper('awodev_aworewards')->storeurl(),
						'trackingId'=>null,
						'fundingConstraint'=>null,
						'senderEmail'=>Mage::getStoreConfig('awodev_aworewards/payout_paypal/'.$env_prefix.'_email'),
					));
			
			$service = Mage::helper('awodev_aworewards/payment_paypal_service_adaptive');
			try { $response = $service->Pay($payRequest); } 
			catch(Exception $ex) {
				return array('error'=>$ex->errorMessage());
			}
			$logger->log('Received payResponse');

			$ack = strtoupper($response->responseEnvelope->ack);
			if($ack != "SUCCESS") return array('error'=>$response->error[0]->message);

			if(strtoupper($response->paymentExecStatus) != 'COMPLETED' ) return array('errorcode'=>11);
		}
				
		
		{ # log payments
			// update rewards tables
			$payment = Mage::getModel('awodev_aworewards/payment');
			$payment->unsId();//unset the rule id so it's considered new
			$payment->setData('payment_type','paypal');
			$payment->setData('user_id',$customer->entity_id);
			$payment->setData('amount_paid',$balance_amount);
			$payment->setData('payment_details',$response->payKey);
			$payment->save();

		
			$collection = Mage::getResourceModel('awodev_aworewards/credit_collection')
				->addFieldToFilter('main_table.id', array('in'=>array_keys($history_rows)))
			;
			foreach($collection as $item) {
				$item->setData('points_paid',$item->getData('points'));
				$item->setData('payment_id',$payment->id);
				$item->save();
			}
		}
					
		{ # add paypal transaction id
		
			
			//retrieve payment transaction 
			$requestEnvelope = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
			$requestEnvelope->initvars(array('detailLevel'=>null, 'errorLanguage'=>'en_US',));

			$paymentDetailsReq = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
			$paymentDetailsReq->initvars(array(
				'requestEnvelope' => $requestEnvelope,
				'payKey' => $response->payKey,
				'transactionId' => null,
				'trackingId' => null,
			));
			$logger->log("Created paymentDetailsRequest Object");

			$service = Mage::helper('awodev_aworewards/payment_paypal_service_adaptive');
			try { $detail_response = $service->PaymentDetails($paymentDetailsReq); } catch(Exception $ex) {}
			
			if(!empty($detail_response->responseEnvelope)) {
				$logger->error("Received paymentDetailsResponse:");
				$ack = strtoupper($detail_response->responseEnvelope->ack);
				if($ack == "SUCCESS") {
					$payment->setData('payment_details',$payment->getData('payment_details')
														.'|'.$detail_response->paymentInfoList->paymentInfo[0]->transactionId
														.'|'.$detail_response->paymentInfoList->paymentInfo[0]->senderTransactionId
					);
					$payment->save();
				}
			}

		}

		 return array('success'=>true);
	}
	
	public function coupon($_type,$user_id=null) {
		if ($user_id == null) $user_id = Mage::getSingleton('customer/session')->getCustomerId();
		$user_id = (int)$user_id;
		if($user_id<=0) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Customer not recognized'));
		}
	
		$customer = Mage::getModel("customer/customer")->load($user_id)->getData();
		if(empty($customer['entity_id'])) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
		}
	
		if($_type!='request' && $_type!='automatic') return array('error'=>Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
		
		$prefix = $_type=='request' ? 'coupon' : 'auto';

		if(!Mage::getStoreConfig('awodev_aworewards/payout_'.$prefix.'/enabled'))  {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Payment not allowed'));
		}

		

		$coupon_template = (int)Mage::getStoreConfig('awodev_aworewards/payout_'.$prefix.'/coupon_template');
		$expiration = (int)Mage::getStoreConfig('awodev_aworewards/payout_'.$prefix.'/expiration');
		$minimum_payout = (float)Mage::getStoreConfig('awodev_aworewards/payout_'.$prefix.'/minimum');
		$point_ratio = (float)Mage::getStoreConfig('awodev_aworewards/payout_'.$prefix.'/point_ratio');
		if($point_ratio<=0) $point_ratio = 1;
		$email_obj = (object) array(
				'email_subject'=>Mage::getStoreConfig('awodev_aworewards/payout_'.$prefix.'/email_subject'),
				'email_template'=>Mage::getStoreConfig('awodev_aworewards/payout_'.$prefix.'/email_template'),
		);
				
		if(empty($email_obj->email_template) || empty($coupon_template)) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Email template not properly set up'));
		}
		
		
        $collection = Mage::getResourceModel('awodev_aworewards/credit_collection')
            ->addFieldToFilter('main_table.user_id', $customer['entity_id'])
            ->addFieldToFilter('main_table.credit_type','points')
            ->addFieldToFilter('main_table.points',array('gt'=>new Zend_Db_Expr('round(IFNULL(main_table.points_paid,0),2)')))
        ;
		
		$collection->getSelect()
				->reset('columns')
				->columns('main_table.id')
				->columns(new Zend_Db_Expr('main_table.points-IFNULL(main_table.points_paid,0) AS balance'))
		;
		$balance = 0;
		$history_rows = array();
		foreach($collection as $item) {
			$row = (object)$item->getData();
			$balance += round($row->balance,2);
			$history_rows[$row->id] = $row;	
		}

		$balance_amount = $balance/$point_ratio;

		if(empty($balance_amount) || $balance_amount<$minimum_payout) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Minimum payout amount not reached'));
		}

		$obj = Mage::helper('awodev_aworewards/coupon')->generateCoupon($coupon_template,null,!empty($expiration) ? $expiration : null);
		if(empty($obj->coupon_code)) {
			return array('error'=>Mage::helper('awodev_aworewards')->__('Coupon code could not be created'));
		}
		
		// fix coupon
		if($obj->rule->getData('simple_action')=='by_percent') $obj->rule->setData('simple_action','cart_fixed');
		$obj->rule->setData('discount_amount',$balance_amount);
		$obj->rule->save();
		
		$coupon_row = new stdclass;
		$coupon_row->id = $obj->coupon_id;
		$coupon_row->coupon_code = $obj->coupon_code;
		$coupon_row->coupon_value = $balance_amount;
		$coupon_row->coupon_value_type = 'amount';
		$coupon_row->expiration = $obj->expiration;

		// update rewards tables
		$payment = Mage::getModel('awodev_aworewards/payment');
		$payment->unsId();//unset the rule id so it's considered new
		$payment->setData('payment_type','mage_coupon');
		$payment->setData('user_id',$customer['entity_id']);
		$payment->setData('coupon_id',$coupon_row->id);
		$payment->setData('amount_paid',$balance_amount);
		$payment->setData('payment_details',$coupon_row->coupon_code);
		$payment->save();
		
		$collection = Mage::getResourceModel('awodev_aworewards/credit_collection')
			->addFieldToFilter('main_table.id', array('in'=>array_keys($history_rows)))
		;
		foreach($collection as $item) {
			$item->setData('points_paid',$item->getData('points'));
			$item->setData('payment_id',$payment->id);
			$item->save();
		}
				
		// send email
		$this->sendPaymentEmail($customer,$coupon_row,$email_obj);

		return array('success'=>true);
		
		
		


	}

	private function sendPaymentEmail($customer,$coupon_row,$email_obj) {
	
		// vendor info
		$FromName = Mage::getStoreConfig('awodev_aworewards/general/email_from_name');
		$MailFrom = Mage::getStoreConfig('awodev_aworewards/general/email_from_email');
		if(empty($FromName)) $FromName = Mage::getStoreConfig('trans_email/ident_general/name'); 
		if(empty($MailFrom)) $MailFrom = Mage::getStoreConfig('trans_email/ident_general/email');

		// determine coupon value
		$coupon_row->coupon_price = '';
		if(!empty($coupon_row->coupon_value)) {
			if($coupon_row->coupon_value_type=='amount') {
				// displays current customer currency, converting coupon value from default currency
				$coupon_row->coupon_price = Mage::helper('core')->currency($coupon_row->coupon_value, true, false);
			}
			else $coupon_row->coupon_price = round($coupon_row->coupon_value).'%';
		}
		
		// tags
		$postvars = new Varien_Object();
		$postvars->setData(array(
					'customer_firstname'=>$customer['firstname'],
					'customer_lastname'=>$customer['lastname'],
					'coupon_code'=>$coupon_row->coupon_code,
					'coupon_value'=>$coupon_row->coupon_price,
					'coupon_expiration'=>!empty($coupon_row->expiration) ? Mage::helper('core')->formatDate($coupon_row->expiration, 'medium', false) : '',
		));
			
		// send email
		$mailer = Mage::getModel('core/email_template');
		$mailer->setDesignConfig(array('area' => 'frontend'))
				->setReplyTo($MailFrom)
				->setTemplateSubject($email_obj->email_subject)
				->sendTransactional(
					$email_obj->email_template,							// template_id
					array('name' => $FromName, 'email' => $MailFrom), 	//sender details
					$customer['email'],									// receiver email
					$customer['firstname']. ' '.$customer['lastname'],	// receiver name
					array('data'=>$postvars)							// extra parameters
				);
		return !$mailer->getSentSuccess();
	}



}

