<?php

require_once '../abstract.php';

class Pmainguet_DeleteUnwantedCommercant extends Mage_Shell_Abstract{

	public function deletecommercant()
	{

		//List options TO DELETE
		$array=[
			"commercant" =>["Vincent Fulco","Lundi / Août","mev@monboucherdandelion.com,myssa25@orange.fr","Michel VAIDIE","Ouverture","Paris Terroirs "]
		];

		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		
		foreach($array as $name => $value){
			$attribute  = Mage::getSingleton("eav/config")->getAttribute("catalog_product", $name);
			foreach ($attribute->getSource()->getAllOptions() as $option) {
			        if(in_array($option['label'],$value)){
			        	echo "Delete ".$option["label"]." from product attribute dataset".PHP_EOL;
			        	$update['delete'][$option['value']] = true;
			        	$update['value'][$option['value']] = true;
			        }
			}
			$setup->addAttributeOption($update);
		}
		die();
	}

	// Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['deletecommercant'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        if (in_array($step, $steps)) {
            $this->$step();
        } else {
            echo "STEP MUST BE ONE OF THESE:\n";
            foreach ($steps as $s) {
                echo $s.",\n";
            }
        }
    }

}

$shell = new Pmainguet_DeleteUnwantedCommercant();
$shell->run();