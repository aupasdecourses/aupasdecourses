<?php

/* To start we need to include abscract.php, which is located 
 * in /shell/abstract.php which contains Magento's Mage_Shell_Abstract 
 * class. 
 *
 * Since this .php is in /shell/Namespace/ we
 * need to include ../ in our require statement which means the
 * file we are including is up one directory from the current file location.
 */
require_once '../abstract.php';

class Pmainguet_DeleteCustomOptions extends Mage_Shell_Abstract{

	public function deletecustom()
	{

		 foreach ($array as $id){
		 	$product = Mage::getModel("catalog/product")->load($id);
		 	echo "Processing ".$product->getName()."\n";
		 	if($product->getOptions() != array()){
		 		foreach ($product->getOptions() as $opt)
		 		{
		 			$opt->delete();
		 		}
		 	$product->setHasOptions(0);
		 	$product->setRequiredOptions(0);
		 	$product->save();
		 	echo "Options removed for ".$product->getName()."\n";
		 	} else {
		 		echo "NO OPTIONS FOR ".$product->getName()."\n";
		 	}
		 }
	}

	// Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['deletecustom'];
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

$shell = new Pmainguet_DeleteCustomOptions();
$shell->run();
