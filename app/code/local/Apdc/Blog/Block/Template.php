<?php

class Apdc_Blog_Block_Template extends Mage_Core_Block_Template
{
    public function getArticlesBlog() {
		$content = file_get_contents('https://www.aupasdecourses.com/blog/feed/');
		$x = new SimpleXmlElement($content);
		$i = 0;
		$articles = array();
		foreach($x->channel->item as $entry) {
			$img = $this->getSkinUrl('/dist/images/img-commercants.jpg');
			if($i > 2) {
				break;
			}
			if($entry->children('media', true)->content->attributes()) {
				$md = $entry->children('media', true)->content->attributes();
				if($md->url) {
					$img = $md->url;
				}
			}
			$articles[] = array(
				'title' => $entry->title,
				'link' => $entry->link,
				'description' => substr(strip_tags($entry->description),0,100).'...',
				'img' => $img
			);
			$i ++;
		}
		return $articles;
		
		/*
			$attributeCode = 'commercant';
			$alias     = 'commercant'.'_table';
			$attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'commercant');
			$collection = Mage::getModel('catalog/product')->getCollection()->getSelect()
					->join(
						array($alias => $attribute->getBackendTable()),
						"e.entity_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
						array($attributeCode => 'value')
					)
					->joinLeft(array('shop_id'=> 'apdc_shop'),$alias.'.value = shop_id.id_shop', array('name'));
		*/
	}
}
