<?php 
class Netreviews_Pla_Helper_Data extends Mage_Core_Helper_Abstract{
	
	public function getGoogleContentArray(){
		$data[] = array(
			"name" => "id",
			"attribute_value"  => "entity_id"
		);
		$data[] = array(
			"name" => "sku",
			"attribute_value"  => "sku"
		);
		$data[] = array(
			"name" => "description",
			"attribute_value" => "description"
		);
		$data[] = array(
			"name" => "link",
			"attribute_value" => "url_path"
		);
		$data[] = array(
			"name" => "image_link",
			"attribute_value"  => "image_link"
		);
		$data[] = array(
			"name" => "brand",
			"attribute_value"  => "brand"
		);
		$data[] = array(
			"name" => "category",
			"attribute_value"  => "category"
		);
		$data[] = array(
			"name" => "gtin",
			"attribute_value"  => "gtin"
		);
		$data[] = array(
			"name" => "mpn",
			"attribute_value"  => "mpn"
		);
		for($i=1;$i<11;$i++){
			$data[] = array(
				"name" => "Extra Info".$i,
				"attribute_value"  => "info".$i
			);
		}
		
        return $data;
        
    }
}