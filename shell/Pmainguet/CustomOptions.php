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

class Pmainguet_CustomOptions extends Mage_Shell_Abstract{

	protected $_optionstitle;

	protected $_idstoclean;

	public function __construct(){
		$this->_optionstitle=[
			"Bouteille au frais (choisir une option)",
			"Parfum",
			"Parfum (choisir une option)",
			"Parfums",
			"Goût (choisir une option)",
			"Produit détaillé/découpé ?",
			'Pain tranché?',
			"Maturité (choisir une option)",
			"Usage (choisir une option)",
			"A détailler (choisir une option)",
			"Type de préparation",
			"Préparation (choisir une option)",
			"Quels chocolats souhaitez vous (cochez une ou plusieurs options)",
			"Choisissez votre taille",
			"Par",
		];

		parent::__construct();
	}

	//List products with bad options
	public function listIds($dump=1,$verbatim=0)
	{
		$products=Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
		$options=array();
		$result=array();
		$noprob=array();
		$inter=array();
		 foreach ($products as $product){
		 	if($product->getData("has_options")=="1"){
		 		$options[]=$product->getId();
		 		if($product->getProductOptionsCollection()->getData()==null){
		 			$inter[$product->getId()]=[
	 					"sku" => $product->getSku(),
	 					"name" => $product->getName(),
	 				];
		 		}
		 		foreach($product->getProductOptionsCollection()->getData() as $option){
		 			if(!in_array($option['default_title'],$this->_optionstitle)){
		 				if($verbatim){
		 					echo $product->getSku()." : ".$option["default_title"].PHP_EOL;
		 				}
		 				$result[$product->getId()]=[
		 					"sku" => $product->getSku(),
		 					"default_title" => $option['default_title'],
		 				];
		 			}else{
		 				$noprob[]=$product->getId();
		 			}
		 		}
		 	}
		 }
		if($dump){
			echo "Nombre de produits au total: ".$products->getSize().PHP_EOL;
			echo "   - dont produits avec options: ".count($options).PHP_EOL;
			echo "       - dont ".count($inter)." avec options mais sans options en fait (ce sont des bundles ou packs a priori))".PHP_EOL;
			echo "       - dont ".count($noprob)." sans problèmes".PHP_EOL;
			echo "       - dont ".count($result)." avec options problématiques: ".PHP_EOL;
		}else{
			return $result;
		}
	}

	public function listIdsVerbatim(){
		$this->listIds(1,1);
	}

	//Clean products options from a list of ids
	public function cleanIds()
	{
		 $total_count=count($this->_idstoclean);
		 $iter=1;
		 foreach ($this->_idstoclean as $id => $data){
		 	$product = Mage::getModel("catalog/product")->load($id);
		 	echo "Processing ".$product->getName().PHP_EOL;
		 	if($product->getOptions() != array()){
		 		foreach ($product->getOptions() as $opt)
		 		{
		 			$opt->delete();
		 		}
		 	$product->setHasOptions(0);
		 	$product->setRequiredOptions(0);
		 	$product->save();
		 	echo $iter." of ".$total_count.": Options removed for ".$product->getName()."\n";
		 	$iter++;
		 	} else {
		 		echo "NO OPTIONS FOR ".$product->getName()."\n";
		 	}
		 }
	}

	//List and clean products with badoptions
	public function listandcleanIds()
	{
		echo "List Ids to clean ...".PHP_EOL;
		$this->_idstoclean=$this->listIds(0);
		echo "Clean ".count($this->_idstoclean)." ids ..".PHP_EOL;
		$this->cleanIds();
		echo "Done".PHP_EOL;

	}

	// Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['listIds','cleanIds','listandcleanIds','listIdsVerbatim'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        if (in_array($step, $steps)) {
            $this->$step();
        } else {
            echo "STEP MUST BE ONE OF THESE:".PHP_EOL;
            foreach ($steps as $s) {
                echo $s.PHP_EOL;
            }
        }
    }

}

$shell = new Pmainguet_CustomOptions();
$shell->run();
