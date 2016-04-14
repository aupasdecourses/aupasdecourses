<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Payment_Paypal_Message extends AwoDev_AwoRewards_Helper_Payment_Paypal {

	/**
	 * @param string $prefix
	 * @return string
	 */
	public function initvars($vars) {
		if(!empty($vars) && is_array($vars)) {
			$vars = (object)$vars;
			$this->thisvars = $vars;
			foreach($vars as $k=>$v) $this->{$k} = $v;
		}
	}
	
	public function toNVPString($prefix = '') {
		return $this->toNVPString_helper($this,$prefix);
	}
	
	public function toNVPString_helper($thisvar,$prefix = '') {
		$nvp = array();
		foreach ($thisvar->thisvars as $property => $defaultValue) {
			
			if (($propertyValue = $thisvar->{$property}) === NULL || $propertyValue == NULL) {
				continue;
			}

			if (is_object($propertyValue)) {
				$nvp[] = $this->toNVPString_helper($propertyValue,$prefix . $property.'.'); //$propertyValue->toNVPString($prefix . $property . '.'); // prefix

			} elseif (is_array($defaultValue) || is_array($propertyValue)) {
				foreach (array_values($propertyValue) as $i => $item) {
					if (!is_object($item)){
                        $nvp[] = $prefix . $property . "($i)" . '=' . urlencode($item);
					}else{
                        $nvp[] = $this->toNVPString_helper($item,$prefix . $property . "($i)."); //$item->toNVPString($prefix . $property . "($i).");
                    }
				}

			} else {
				// Handle classes with attributes
				if($property == 'value' && ($anno = Mage::helper('awodev_aworewards/payment_paypal_utils')->propertyAnnotations($this, $property)) != NULL && isset($anno['value']) ) {
					$nvpKey = substr($prefix, 0, -1); // Remove the ending '.'
				} else {
					$nvpKey = $prefix . $property ;
				}
				$nvp[] = $nvpKey . '=' . urlencode($propertyValue);
			}
		}

		return implode('&', $nvp);
	}


	/**
	 * @param array $map
	 * @param string $prefix
	 */
	public function init(array $map = array(), $prefix = '') {
		foreach($map as $key=>$value) {
			$parts = explode('.',$key);
			
			$it = &$this;
			foreach($parts as $part) {
				if(preg_match('/^(.*?)\((\d+)\)$/',$part,$match)){
					$it = &$it->{$match[1]}[$match[2]];
				}
				else $it = &$it->{$part};
			}
			$it = $value;
		}
	}

	private function isBuiltInType($typeName) {
		static $types = array('string', 'int', 'integer', 'bool', 'boolean', 'float', 'decimal', 'long', 'datetime', 'double');
		return in_array(strtolower($typeName), $types);
	}
}
