<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_InvitationController extends Mage_Core_Controller_Front_Action {

	public function preDispatch() {
		parent::preDispatch();

		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->setFlag('', 'no-dispatch', true);
			$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
		}
	}

	public function indexAction() {

		$this->loadLayout();
        //$this->getLayout()->getBlock('awodev_aworewards/invitation');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
	}

	public function ajaxUploadOneAction() {
		
		$session = Mage::getSingleton('customer/session');

		$u_contacts = $session->getData('aworewards_email_list_upload');
		if(empty($u_contacts)) $u_contacts = array();

		$email = $this->getRequest()->getParam('email');
		if(!empty($email)) {
			if(Zend_Validate::is($email, 'EmailAddress')) {
				if(empty($u_contacts[$email])) {
					$u_contacts[$email] = array('name'=>'','email'=>$email);
					ksort($u_contacts);
					$session->setData('aworewards_email_list_upload',$u_contacts);
				}
			}
		}
		if(!empty($u_contacts)) {
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');
			$select = $read->select()
				->from($resource->getTableName('awodev_aworewards/referral'), 'email')
				->where('user_id=?', Mage::getSingleton('customer/session')->getCustomerId());
			$used_emails = $read->fetchCol($select);
			
			$allowed = array('google','yahoo');
			$contacts = array();
			foreach($allowed as $getter) {
				$tmp_contacts = $session->getData('aworewards_email_list_'.$getter);
				if(!empty($tmp_contacts)) $contacts[$getter] = $tmp_contacts;
			}
			//$contacts['upload'] = $u_contacts;
			$burned_emails = array();
			foreach($contacts as $emails) {
				if(empty($emails)) continue;
				foreach($emails as $email=>$r) $burned_emails[$email] = 1;
			}
			
			$tmp_contacts = $u_contacts;
			$u_contacts = array();
			foreach($tmp_contacts as $email=>$r) {
				if(!empty($burned_emails[$email])) continue;
				if(!empty($used_emails[$email])) continue;
								
				$u_contacts[$email] = $r;
			}
		}
		echo Mage::helper('core')->jsonEncode($u_contacts);
		exit;

	}
	public function importEmailAction() {
		
		$session = Mage::getSingleton('customer/session');
	
		$contacts = $session->getData('aworewards_email_list_upload');
		if(empty($contacts)) $contacts = array();

		$file = $_FILES;
		$lines = file($file['file']['tmp_name']);

		foreach($lines as $email) {
			if(empty($email)) continue;
			$email = strtolower(trim($email));
			if(!Zend_Validate::is($email, 'EmailAddress')) continue;
			if(!empty($contacts[$email])) continue;
			
			$contacts[$email] = array('name'=>'','email'=>$email);
		}
		
		
		ksort($contacts);
					
		$session->setData('aworewards_email_list_upload',$contacts);
    	$this->_redirect("*/*/");

	}

	public function socialMailAction() {

		$allowed_types = array('google','yahoo');
		$getter = $this->getRequest()->getPost('getter');
		if(!in_array($getter,$allowed_types)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'sm1'));
			return;
		}
		
		$oauth = Mage::helper('awodev_aworewards/oauth_'.$getter);
		if($getter=='yahoo') $callback = Mage::getUrl('*/*/socialmailconfirm',array('getter'=>$getter));
		else $callback = Mage::getUrl('*/*/socialmailconfirmoauth2',array('getter'=>$getter));
		
		
		$oauth->init($callback);
		$access_token=$oauth->get_request_token(false, true);
		if($oauth->http_code==200 && !empty($access_token['oauth_token']) && !empty($access_token['oauth_token_secret'])) {
			$url = $oauth->get_login_url($access_token,true,'postpublic');
			$this->_redirectUrl($url);
			//$this->getResponse()->setRedirect($url);
			return;
		}
		else {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Error retrieving authentitcation key'));
			$this->_redirect("*/*/",array('x'=>'sm2'));
			return;
		}
		
	}
	public function socialMailConfirmAction() {
		
		$getter = $this->getRequest()->getParam('getter');
		$allowed = array('google','yahoo');
		if(empty($getter) || !in_array($getter,$allowed)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'smc1'));
			return;
		}
		
		$session = Mage::getSingleton('customer/session');
		$contacts = $session->getData('aworewards_email_list_'.$getter);
		if(!empty($contacts)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Contacts already loaded'));
			$this->_redirect("*/*/",array('x'=>'smc2'));
			return;
		}
		

		$oauth_verifier = $this->getRequest()->getParam('oauth_verifier');
		$g_oauth_token = $this->getRequest()->getParam('oauth_token');
		$oauth_token = $session->getData('aworewards_oauth_token');;
		$oauth_token_secret = $session->getData('aworewards_oauth_token_secret');
		
		if(empty($oauth_verifier) || empty($oauth_token) || empty($oauth_token_secret) || empty($g_oauth_token) || $g_oauth_token!=$oauth_token) {
		// something is wrong
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'smc3'));
			return;
		}
		
		$oauth = Mage::helper('awodev_aworewards/oauth_'.$getter);
		$oauth->init();
		$contact_access = $oauth->get_access_token($oauth_token, $oauth_token_secret, $oauth_verifier, false, true);
		$contacts= $oauth->get_contacts($contact_access,500);
		ksort($contacts);
		
		$session->setData('aworewards_email_list_'.$getter,$contacts);
		Mage::getSingleton('customer/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Emails uploaded'));
		$this->_redirect("*/*/",array('x'=>'smc3'));
		return;
	}
	public function socialMailConfirmOauth2Action() {
		
		$getter = $this->getRequest()->getParam('getter');
		$allowed = array('google','yahoo');
		if(empty($getter) || !in_array($getter,$allowed)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'smco1'));
			return;
		}
		
		$session = Mage::getSingleton('customer/session');
		$contacts = $session->getData('aworewards_email_list_'.$getter);
		if(!empty($contacts)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Contacts already loaded'));
			$this->_redirect("*/*/",array('x'=>'smco2'));
			return;
		}
		
		$code = $this->getRequest()->getParam('code');
		if(empty($code)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'smco3'));
			return;
		}


		$callback = Mage::getUrl('*/*/socialmailconfirmoauth2',array('getter'=>$getter));

		$oauth = Mage::helper('awodev_aworewards/oauth_'.$getter);
		$oauth->init($callback);
		$contacts= $oauth->get_contacts($code,500);
		ksort($contacts);
		
		$session->setData('aworewards_email_list_'.$getter,$contacts);
		Mage::getSingleton('customer/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Emails uploaded'));
		$this->_redirect("*/*/",array('x'=>'smco4'));
		return;
	}
	
	public function socialPostAction() {

		$session = Mage::getSingleton('customer/session');

		$allowed_types = array('facebook'=>array('postpublic'),'twitter'=>array('postpublic'));
		$getter = $this->getRequest()->getPost('getter');
		$getter_type = $this->getRequest()->getPost('getter_type');
		if(empty($allowed_types[$getter]) || !in_array($getter_type,$allowed_types[$getter])) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'sp1'));
			return;
		}

		$invitation_id = $this->getRequest()->getPost('invitation_id');
		$invitation_row = Mage::getModel('awodev_aworewards/invitation')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',Mage::app()->getWebsite()->getId())
					->addFieldToFilter('id',$invitation_id)
					->addFieldToFilter('invitation_type',$getter)
					->getFirstItem()
					->getDataFront()
		;
		if(empty($invitation_row)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'sp2'));
			return;
		}
		$invitation_row = (object) $invitation_row;
		//echo '<pre>'; print_r($invitation_row);exit;
		

		$customer = Mage::getModel("customer/customer")->load(Mage::getSingleton('customer/session')->getCustomerId())->getData();
		$customer_link = Mage::helper('awodev_aworewards/data')->registration_link();

		$customer_name = $customer['firstname'].' '.$customer['lastname'];

		$param_message = $invitation_row->email_body;
		$custommessage = trim($this->getRequest()->getPost('custommessage_'.$invitation_row->id));
		if($custommessage== Mage::helper('awodev_aworewards')->__('Your message here...')) $custommessage = '';
		$body	= Mage::helper('awodev_aworewards')->br2nl(trim(str_replace(array('{user_name}','{customer_note}','{customer_link}'),array($customer_name,$custommessage,$customer_link),$param_message)));
		//$body	= strip_tags(Mage::helper('awodev_aworewards')->br2nl(trim(str_replace(array('{user_name}','{customer_note}','{customer_link}'),array($customer_name,$custommessage,$customer_link),$param_message))));
		if(empty($body)) return;
			
		Mage::getSingleton('customer/session')->setData('aworewards_invitation_message_'.$getter, $body);
		
		
		$callback = Mage::getUrl('*/*/socialpostconfirm',array('getter'=>$getter,'getter_type'=>$getter_type,'invitation_id'=>$invitation_row->id));
		
		
		$oauth = Mage::helper('awodev_aworewards/oauth_'.$getter);
		$oauth->init($callback);
		$access_token=$oauth->get_request_token(false, true);
		if($oauth->http_code==200 && !empty($access_token['oauth_token']) && !empty($access_token['oauth_token_secret'])) {
			$url = $oauth->get_login_url($access_token); 
			$this->_redirectUrl($url);
			return;
		}
		else {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Error retrieving authentitcation key'));
			$this->_redirect("*/*/",array('x'=>'sp3'));
			return;
		}
		
	}


	public function socialPostConfirmAction() {
		
		$redirect_url = $this->getRequest()->getParam('redirect_url');
		$redirect_url = !empty($redirect_url) ? Mage::helper('core')->urlDecode($redirect_url) : '*/*/';
	
		$allowed_types = array('facebook'=>array('postpublic'),'twitter'=>array('postpublic'));
		$getter = $this->getRequest()->getParam('getter');
		$getter_type = $this->getRequest()->getParam('getter_type');
		if(empty($allowed_types[$getter]) || !in_array($getter_type,$allowed_types[$getter])) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect($redirect_url,array('x'=>'spc1'));
			return;
		}

		$invitation_id = $this->getRequest()->getParam('invitation_id');
		$invitation_row = Mage::getModel('awodev_aworewards/invitation')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',Mage::app()->getWebsite()->getId())
					->addFieldToFilter('id',$invitation_id)
					->addFieldToFilter('invitation_type',$getter)
					->getFirstItem()
					->getDataFront()
		;
		if(empty($invitation_row)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'spc2'));
			return;
		}
		$invitation_row = (object) $invitation_row;


		$body = Mage::getSingleton('customer/session')->getData('aworewards_invitation_message_'.$getter);
		if(empty($body)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'spc3'));
			return;
		}
		

		$oauth_token = $this->getRequest()->getParam($getter=='facebook' ? 'code' : 'oauth_token');
		if(empty($oauth_token)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'spc4'));
			return;
		}

		$oauth_verifier = $this->getRequest()->getParam('oauth_verifier');
		$oauth_token_secret = Mage::getSingleton('customer/session')->getData('aworewards_oauth_token_secret');

		
		
		
		
		
		
		if(strpos($body,'{coupon_code}')!==false) {
			$coupon_template = $invitation_row->coupon_template;
			$coupon_expiration = (int)$invitation_row->coupon_expiration;
			if($coupon_expiration<0) $coupon_expiration = 0;
		}
		$coupon_code = '';
		if(!empty($coupon_template)) {
			$obj = Mage::helper('awodev_aworewards/coupon')->generateCoupon($coupon_template,null,!empty($coupon_expiration) ? $coupon_expiration : null);
			if(!empty($obj->coupon_code)) $coupon_code = $obj->coupon_code;
		}
		$body	= str_replace(array('{coupon_code}'),$coupon_code,$body);
		
		
		$callback = Mage::getUrl('*/*/socialpostconfirm',array('getter'=>$getter,'getter_type'=>$getter_type,'invitation_id'=>$invitation_row->id));

		$oauth = Mage::helper('awodev_aworewards/oauth_'.$getter);
		$oauth->init($callback);
		$contact_access = $oauth->get_access_token($oauth_token, $oauth_token_secret, $oauth_verifier, false, true);
		$rtn = $oauth->$getter_type($contact_access,$body);
		if(!empty($rtn['error']) || empty($rtn['id'])) {
			if(!empty($obj)) {
				Mage::helper('awodev_aworewards/coupon')->deleteCoupon($obj->coupon_id);
			}
			
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'spc5'));
			return;
		}

		Mage::getSingleton('customer/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Message posted'));
		$this->_redirect("*/*/",array('x'=>'spc6'));
		return;
		
	}

	
	
	public function sendInviteAction() {
//printrx($post);
		$customer = Mage::getModel("customer/customer")->load(Mage::getSingleton('customer/session')->getCustomerId())->getData();
		$customer_name = $customer['firstname'].' '.$customer['lastname'];
		$user_id = $customer['entity_id'];
		if(empty($customer)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'si1'));
			return;
		}
		
		$cando = true;
		$check_order = (int) Mage::getStoreConfig('awodev_aworewards/invitation/order_required');
		if($check_order == 1) {
			$cando = Mage::helper('awodev_aworewards')->isCustomerOrdered();
		}
		if(!$cando) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Please place an order to have access to Refer a Friend'));
			$this->_redirect("*/*/",array('x'=>'si2'));
			return;
		}

		$invitation_id = $this->getRequest()->getPost('invitation_id');
		if(empty($invitation_id)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'si3'));
			return;
		}
		
		$invitation_row = Mage::getModel('awodev_aworewards/invitation')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',Mage::app()->getWebsite()->getId())
					->addFieldToFilter('id',$invitation_id)
					->addFieldToFilter('invitation_type','email')
					->getFirstItem()
					->getDataFront()
		;
		if(empty($invitation_row)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'si4'));
			return;
		}
		$invitation_row = (object) $invitation_row;


		// Here is the meat and potatoes of the header injection test.  We iterate over the array of form input 
		// and check for header strings. If we fine one, send an unauthorized header and die.
		$headers = array (	'Content-Type:', 'MIME-Version:', 'Content-Transfer-Encoding:', 'bcc:', 'cc:');
		$fields = array ('mailto', 'sender', 'from', 'subject',);
		foreach ($fields as $field){ foreach ($headers as $header){ if (!empty($_POST[$field]) && strpos($_POST[$field], $header) !== false){ 
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid Configuration'));
			$this->_redirect("*/*/",array('x'=>'si5'));
		} } }
		unset ($headers, $fields);

		
		$FromName = Mage::getStoreConfig('awodev_aworewards/general/email_from_name');
		$MailFrom = Mage::getStoreConfig('awodev_aworewards/general/email_from_email');
		if(empty($FromName)) $FromName = Mage::getStoreConfig('trans_email/ident_general/name'); 
		if(empty($MailFrom)) $MailFrom = Mage::getStoreConfig('trans_email/ident_general/email');



		$param_subject = $invitation_row->email_subject;
		$param_message = $invitation_row->email_body;
		
		$customer_link = Mage::helper('awodev_aworewards/data')->registration_link();
		$customer_link = '<a href="'.$customer_link.'">'.$customer_link.'</a>';
		
		
		$max 		= (int) Mage::getStoreConfig('awodev_aworewards/invitation/maxemailperinvite'); //30
		$maxperday  = (int) Mage::getStoreConfig('awodev_aworewards/invitation/maxinvitesperday'); //100
		$delay 		= (int) Mage::getStoreConfig('awodev_aworewards/invitation/delaybetweeninvites'); //0

		if(empty($MailFrom) || !Zend_Validate::is($MailFrom, 'EmailAddress')) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid shop email'));
			$this->_redirect("*/*/",array('x'=>'si6'));
			return;
		}

		if ( $delay ) {
			$checkdelay = 1;
			$ts 		= time();		
			$result = Mage::getModel('awodev_aworewards/referral')
						->getCollection()
						->addFieldToFilter('user_id',$user_id)
						->addFieldToFilter('ip',$_SERVER["REMOTE_ADDR"])
						->setOrder('send_date', 'DESC')
						->getFirstItem()
						->getData()
			;
			if(!empty($result['send_date'])) {
				$lasttime = strtotime($result) + $delay;				
				if ( $lasttime > $ts )$checkdelay = 0;
			}
			if ( !$checkdelay ) {
				Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Delay between invites invalid'));
				$this->_redirect("*/*/",array('x'=>'si7'));
				return;
			} 
		}
		
		
		$currentmaxperday = Mage::getModel('awodev_aworewards/referral')
					->getCollection()
					->addFieldToFilter('user_id',$user_id)
					->addFieldToFilter('ip',$_SERVER["REMOTE_ADDR"])
					//->addFieldToFilter(array(array('attribute' => 'send_date', 'like' => date( 'Y-m-d').'%')))
		;
		$currentmaxperday->getSelect()->where('send_date LIKE "'.date('Y-m-d').'%"');
		$currentmaxperday = $currentmaxperday->getSize();	
		if ( !empty($maxperday) && $currentmaxperday >= $maxperday ) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Max invites per day reached'));
			$this->_redirect("*/*/",array('x'=>'si8'));
			return;
		}


		// Build the message to send
		$emails_yahoo = $this->getRequest()->getPost('email_to_import_yahoo');
		$emails_google = $this->getRequest()->getPost('email_to_import_google');
		$emails_upload = $this->getRequest()->getPost('email_to_import_upload');
		$emails_manual = $this->getRequest()->getPost('email_to');
		if(empty($emails_yahoo)) $emails_yahoo = array();
		if(empty($emails_google)) $emails_google = array();
		if(empty($emails_upload)) $emails_upload = array();
		if(empty($emails_manual)) $emails_manual = array();
		
		$emails = array_unique(array_merge($emails_yahoo,$emails_google,$emails_upload,$emails_manual));
		
		$custommessage = $this->getRequest()->getPost('custommessage_'.$invitation_row->id);
		if($custommessage== Mage::helper('awodev_aworewards')->__('Your message here...')) $custommessage = '';
		$subject		 	= $param_subject;
		
		$body	= str_replace(array('{user_name}','{customer_note}','{customer_link}'),array($customer_name,nl2br($custommessage),$customer_link),$param_message);		
		if(empty($body)) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid configuration'));
			$this->_redirect("*/*/",array('x'=>'si9'));
			return;
		}
			
		$counter = $err_invalid = $err_sent = $err = 0;
		$email_err_list = array();
		$email_list = array();
		
		
		// coupon code
		if(strpos($body,'{coupon_code}')!==false) {
			$coupon_template = $invitation_row->coupon_template;
			$coupon_expiration = (int)$invitation_row->coupon_expiration;
			if($coupon_expiration<0) $coupon_expiration = 0;
		}

		
		foreach ($emails as $email) {
			if(empty($email)) continue;
			
			//extractEmailsFromString
			if(false !== preg_match_all('`\w(?:[-_.]?\w)*@\w(?:[-_.]?\w)*\.(?:[a-z]{2,4})`', strtolower($email), $aEmails)) {
				if(is_array($aEmails[0]) && sizeof($aEmails[0])>0) {
					$email = $aEmails[0][0];
				} else $email = null;
			} else $email = null;
		
			
			if(!Zend_Validate::is($email, 'EmailAddress')) {
				$email_err_list[$email] = Mage::helper('awodev_aworewards')->__('Email invalid');
				$err_invalid++;
				continue;
			}
			
			$tester = Mage::getModel("customer/customer")
						->setWebsiteId($customer['website_id'])
						->loadByEmail($email)
						->getData()
			;
			if(!empty($tester)) {
				$email_err_list[$email] = Mage::helper('awodev_aworewards')->__('Email is already registered');
				$err_sent++;
				continue;
			}
			
			$tester = Mage::getModel('awodev_aworewards/referral')
						->getCollection()
						->addFieldToFilter('website_id',$customer['website_id'])
						->addFieldToFilter('email',$email)
						->getFirstItem()
						->getData()
			;
			if(!empty($tester)) {
				$email_err_list[$email] = Mage::helper('awodev_aworewards')->__('Email is already sponsored');
				$err_sent++;
				continue;
			}
			
			
			
			$coupon_code = '';
			if(!empty($coupon_template)) {
				$obj = Mage::helper('awodev_aworewards/coupon')->generateCoupon($coupon_template,null,!empty($coupon_expiration) ? $coupon_expiration : null);
				if(!empty($obj->coupon_code)) $coupon_code = $obj->coupon_code;
			}
			$body	= str_replace(array('{coupon_code}'),$coupon_code,$body);
			
			
			$mailer = Mage::getModel('core/email')
						//->setToName('')
						->setToEmail($email)
						->setBody($body)
						->setSubject($subject)
						->setFromEmail($MailFrom)
						->setFromName($FromName)
						->setType('html')// YOu can use Html or text as Mail format
			;
			try {
				$mailer->send();
			}
			catch (Exception $e) {
				if(!empty($coupon_code)) {
					Mage::helper('awodev_aworewards/coupon')->deleteCoupon($obj->coupon_id);
				}
				$email_err_list[$email] = Mage::helper('awodev_aworewards')->__('Error sending Email');
				$err++;
				continue;
			}

								
			$email_list[] = $email;
			{ // Insert UserID, IP and email insertInfos
	
		
				$now		= date( 'Y-m-d H:i:s' );
				$ref = Mage::getModel('awodev_aworewards/referral');
				$ref->setData('user_id',$customer['entity_id']);
				$ref->setData('ip',$_SERVER["REMOTE_ADDR"]);
				$ref->setData('email',$email);
				$ref->setData('send_date',$now);
				$ref->setData('last_sent_date',$now);
				$ref->setData('customer_msg',$custommessage);
				$ref->setData('invitation_id',$invitation_row->id);
				$ref->setData('coupon_code',$coupon_code);
				$ref->save();
			}
			$counter++;
			$currentmaxperday++;
			if ( $counter==$max || $currentmaxperday==$maxperday )	break;
		}

		
		$is_sponsor_enabled  = (int) Mage::getStoreConfig('awodev_aworewards/sponsor/mail_stat_enabled');
		if(!empty($is_sponsor_enabled)) {
			$err_message = '';
			if(!empty($email_err_list)) {
				foreach($email_err_list as $email=>$errmsg) $err_message .= '<div>'.$email.': '.$errmsg.'</div>';
			}
			$postvars = new Varien_Object();
			$postvars->setData(array(
					'user_firatname'=>$customer['firstname'],
					'user_lastname'=>$customer['lastname'],
					'sent_mail_list'=>implode('<br />',$email_list),
					'err_mail_list'=>$err_message)
			);
			$mailer = Mage::getModel('core/email_template');
			$mailer->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($MailFrom)
					->setTemplateSubject(Mage::getStoreConfig('awodev_aworewards/sponsor/mail_stat_email_subject'))
					->sendTransactional(
						Mage::getStoreConfig('awodev_aworewards/sponsor/mail_stat_email_template'),
						array('name' => $FromName, 'email' => $MailFrom),
						$customer['email'],
						$customer_name,
						array('data'=>$postvars)
					);
			if (!$mailer->getSentSuccess());

		}
		
		
		$query = array();
		if(!empty($counter)) Mage::getSingleton('customer/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Invitation(s) sent').': '.$counter);
		if(!empty($err_sent)) Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Email(s) already registered or sponsored').': '.$err_sent);
		if(!empty($err_invalid)) Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Invalid email(s)').': '.$err_invalid);
		if(!empty($err)) Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__('Error sending email(s)').': '.$err);
		
		
		$this->_redirect("*/*/");
		
		// Display
		return true;

	}
	
	
}

