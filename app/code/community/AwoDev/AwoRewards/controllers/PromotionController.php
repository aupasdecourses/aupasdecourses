<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_PromotionController extends Mage_Core_Controller_Front_Action {

	public function indexAction() {

		$this->loadLayout();
		//$this->getLayout()->getBlock('head')->addCss('css/awodev/aworewards/promotion.css');
		

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
	}

	
	public function socialPostAction() {

		$session = Mage::getSingleton('customer/session');

		$allowed_types = array('facebook'=>array('postpublic','likeus'),'twitter'=>array('postpublic','likeus'));
		$getter = $this->getRequest()->getPost('getter');
		$getter_type = $this->getRequest()->getPost('getter_type');
		
		$redirect_url = $this->getRequest()->getPost('return');
		$redirect_url = !empty($redirect_url) ? Mage::helper('core')->urlDecode($redirect_url) : Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$redirect_url = $this->remove_querystring_var($redirect_url,'x');

		if(empty($allowed_types[$getter]) || !in_array($getter_type,$allowed_types[$getter])) 
			return $this->myredirect($redirect_url,'pr1','Invalid Configuration');


		if(!Mage::helper('customer')->isLoggedIn()) return $this->myredirect($redirect_url,'pr2','Please log in');

		
		$rule = (int)$this->getRequest()->getPost('rule');
		if(empty($rule)) return $this->myredirect($redirect_url,'pr3','Invalid Configuration');

		{ // facebook reward, already verified through javascript
			if($getter=='facebook' && $getter_type=='likeus') {
			
				Mage::helper('awodev_aworewards/reward_social')->processSocial(array(
						'type'=>$getter.'_'.$getter_type,
						'user_id'=>Mage::getSingleton('customer/session')->getCustomer()->getId(),
						'rule_id'=>$rule,
						'post_id'=>''
					)
				);
			
				return $this->myredirect($redirect_url,'pr5','','Successfully posted');
			}
		}
	
		$callback = Mage::getUrl('*/*/socialpostconfirm',array('getter'=>$getter,'getter_type'=>$getter_type,'rule'=>$rule,'redirect_url'=>Mage::helper('core/url')->getEncodedUrl($redirect_url)));
		
		
		$oauth = Mage::helper('awodev_aworewards/oauth_'.$getter);
		$oauth->init($callback);
		$access_token=$oauth->get_request_token(false, true);
		if($oauth->http_code==200 && !empty($access_token['oauth_token']) && !empty($access_token['oauth_token_secret'])) {
			$url = $oauth->get_login_url($access_token,null,$getter_type); 
			$this->_redirectUrl($url);
			return;
		}
		
		return $this->myredirect($redirect_url,'pr4','Error retrieving authentitcation key');
		
	}

	public function socialPostConfirmAction() {
	
		$allowed_types = array('facebook'=>array('postpublic','likeus'),'twitter'=>array('postpublic','likeus'));
		$getter = $this->getRequest()->getParam('getter');
		$getter_type = $this->getRequest()->getParam('getter_type');
		$rule_id = (int)$this->getRequest()->getParam('rule');
		
		$redirect_url = $this->getRequest()->getParam('redirect_url');
		$redirect_url = !empty($redirect_url) ? Mage::helper('core')->urlDecode($redirect_url) : '*/*/';
		//$redirect_url = $this->remove_querystring_var($redirect_url,'x');
		
		if(empty($allowed_types[$getter]) || !in_array($getter_type,$allowed_types[$getter])) 
			return $this->myredirect($redirect_url,'prc1','Invalid Configuration');


		if(empty($rule_id)) return $this->myredirect($redirect_url,'prc2','Invalid Configuration');
				

		$oauth_token = $this->getRequest()->getParam($getter=='facebook' ? 'code' : 'oauth_token');
		if(empty($oauth_token)) return $this->myredirect($redirect_url,'prc3','Invalid Configuration');

		$oauth_verifier = $this->getRequest()->getParam('oauth_verifier');
		$oauth_token_secret = Mage::getSingleton('customer/session')->getData('aworewards_oauth_token_secret');


		$rule_items = (object)Mage::getModel('awodev_aworewards/rule')->load($rule_id)->getDataFront();
		$variable = '';
		if($getter_type=='postpublic') $variable = @htmlspecialchars_decode($rule_items->predefined_text);
		elseif($getter_type=='likeus') $variable = @$rule_items->params->username;
		if(empty($variable)) return $this->myredirect($redirect_url,'prc4','Invalid Configuration');
		
		
		$callback = Mage::getUrl('*/*/socialpostconfirm',array('getter'=>$getter,'getter_type'=>$getter_type,'rule'=>$rule_id,'redirect_url'=>Mage::helper('core/url')->getEncodedUrl($redirect_url)));
		
		$oauth = Mage::helper('awodev_aworewards/oauth_'.$getter);
		$oauth->init($callback);
		$contact_access = $oauth->get_access_token($oauth_token, $oauth_token_secret, $oauth_verifier, false, true);
		$rtn = $oauth->$getter_type($contact_access,$variable);
		if(!empty($rtn['error']) || empty($rtn['id']))
			return $this->myredirect($redirect_url,'prc5','Invalid Configuration');

		Mage::helper('awodev_aworewards/reward_social')->processSocial(array(
				'type'=>$getter.'_'.$getter_type,
				'user_id'=>Mage::getSingleton('customer/session')->getCustomer()->getId(),
				'rule_id'=>$rule_id,
				'post_id'=>$rtn['id']
			)
		);


		return $this->myredirect($redirect_url,'prc6','','Successfully posted');
		
	}
	
	

	public function remove_querystring_var($url, $key) {
		$url_parts = parse_url($url);
		if(empty($url_parts['query'])) return $url;
		
		parse_str($url_parts['query'], $result_array);
		unset($result_array[$key]);
		$url_parts['query'] = http_build_query($result_array);
		$url = (isset($url_parts["scheme"])?$url_parts["scheme"]."://":"").
			(isset($url_parts["user"])?$url_parts["user"].":":"").
			(isset($url_parts["pass"])?$url_parts["pass"]."@":"").
			(isset($url_parts["host"])?$url_parts["host"]:"").
			(isset($url_parts["port"])?":".$url_parts["port"]:"").
			(isset($url_parts["path"])?$url_parts["path"]:"").
			(!empty($url_parts["query"])?"?".$url_parts["query"]:"").
			(isset($url_parts["fragment"])?"#".$url_parts["fragment"]:"");
		return $url;
	}
	
	public function myredirect($url,$x='',$err='',$success='') {
		if(!empty($x)) {	
			$separator = strpos($url,'?')===false ? '?' : '&';
			$url .= $separator.'x='.$x;
		}

		if(!empty($err))  Mage::getSingleton('customer/session')->addError(Mage::helper('awodev_aworewards')->__($err));
		if(!empty($success))  Mage::getSingleton('customer/session')->addSuccess(Mage::helper('awodev_aworewards')->__($success));
		$this->_redirectUrl($url);
		
		return;
	}
}

