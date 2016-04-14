<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Locale extends Mage_Core_Model_Abstract {

    protected function _construct() {  
        $this->_init('awodev_aworewards/locale');
    } 
	
	public function setEntity($table,$table_id,$data) {
		if(empty($table)) return;
		if(empty($data) || !is_array($data)) return;
		
		foreach($data as $col=>$langs) {
			if(empty($langs)) {
			// delete items
				$collections = $this->getCollection()
					->addFieldToFilter('entity', $table)
					->addFieldToFilter('entity_id', $table_id)
					->addFieldToFilter('col', $col)
				;
				foreach ($collections as $collection) {
					$collection->delete();
				}
				continue;
			}
			foreach($langs as $store_id=>$value) {

				$collections = $this->getCollection()
					->addFieldToFilter('entity', $table)
					->addFieldToFilter('entity_id', $table_id)
					->addFieldToFilter('store_id', $store_id)
					->addFieldToFilter('col', $col)
				;
				$item = $collections->getFirstItem();
				if(empty($value)) {
				// delete item
					$item->delete();
				}
				else {
				// add/update item to table
					if($item->isObjectNew()) {
						$item->entity = $table;
						$item->entity_id = $table_id;
						$item->store_id = $store_id;
						$item->col = $col;
					}
					$item->value = $value;
					$item->save();

				}
			}
		}
		return true;
	
	}

	public function getEntity($table,$table_id) {
	
		$locale = array();
		$collection = $this->getCollection()
					->addFieldToFilter('entity', $table)
					->addFieldToFilter('entity_id', (int)$table_id)
		;
		foreach($collection as $item) {
			$locale[$item->getData('col')][$item->getData('store_id')] = $item->getData('value');
		}
		return $locale;
	}
	
	public function deleteEntity($table,$table_id) {
		if(empty($table_id)) return; 
		$locale = array();
		$collection = $this->getCollection()
					->addFieldToFilter('entity', $table)
					->addFieldToFilter('entity_id', (int)$table_id)
		;
		foreach($collection as $item) {
			$item->delete();
		}
	}
	
}