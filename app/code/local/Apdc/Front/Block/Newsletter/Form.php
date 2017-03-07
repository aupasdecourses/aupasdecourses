<?php 

class Apdc_Front_Block_Newsletter_Form extends Ebizmarts_MageMonkey_Block_Lists
{

	private $_listcustom=[
			"Paris 1er",
			"Paris 2e",
			"Paris 3e",
			"Paris 4e",
			"Paris 5e",
			"Paris 6e",
			"Paris 7e",
			"Paris 8e",
			"Paris 9e",
			"Paris 10e",
			"Paris 11e",
			"Paris 12e",
			"Paris 13e",
			"Paris 14e",
			"Paris 15e",
			"Paris 16e",
			"Paris 17e",
			"Paris 18e",
			"Paris 19e",
			"Paris 20e",
			"Levallois Perret",
			"Neuilly sur Seine",
			"Boulogne Billancourt",
			"Vincennes",
			"Issy-les-Moulineaux",
		];

	public function getNewsletterlist(){
		
		$mclists=$this->getLists();
		$mclists_size=count($mclists);
		$arr=array();
		
		foreach($this->_listcustom as $q){
			foreach($mclists as $key=>$ql){
				if($ql['name']==$q){
					$arr[$q]=$ql['id'];
					unset($mclists[$key]);
					break;
				}
			}
		}

		if($mclists_size<>count($arr)){
			Mage::log('Newsletter entry missing on landing page newsletter form',null,'Ebizmart_Newsletter_Form.log');
		}

		return $arr;

	}

}