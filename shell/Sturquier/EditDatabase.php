<?php
/* To start we need to include abstract.php which is located
 * in /shell/abstract.php which contains Magento's Mage_Shell_Abstract 
 * class. 
 *
 * Since this .php is in /shell/Namespace/ we
 * need to include ../ in our require statement which means the
 * file we are including is up one directory from the current file location.
 */
require_once '../abstract.php';

class Sturquier_EditDatabase extends Mage_Shell_Abstract
{
	
	public function run()
	{
		
	$order = Mage::getModel('amorderattach/order_field');	
	$orders = $order->getCollection();

	$comment_client ='commentaire du client';
	$comment_commercant ='commentaire du commercant';

	
	foreach($orders as $ord)
	{
	if (is_null(Mage::getSingleton('amorderattach/order_field')->getCollection()->addFieldToFilter('commentaire_client', $comment_client)))
	{
		$data = array(
			'commentaire_client' => $comment_client,
			'commentaire_commercant' => $comment_commercant,
		);

		try {
			$new_orders = Mage::getModel('amorderattach/order_field')->setData($data);
			$new_orders->save();
			echo "Columns added.\n";
		} catch (Exception $e)
		{
			echo "Unable to add columns.\n";
		}
		echo "It works.\n";
	}
	
		$data = $ord->getData();

		/* SUPPRESSION COLONNE SCREENSHOT */
		/*try 
		{
			unset($data['screenshot']);
			$new_orders = Mage::getModel('amorderattach/order_field')->setData($data);
			$new_orders->save();
			echo "Screenshot remoted.\n";
		} catch (Exception $e) 
		{
			echo $e->getMessage()."\n";
		}
		 */
/*
  $order->addColumn('commentaire_client', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'nullable' => true,
			'default'  => '',
		))
		->addColumn('commentaire_commercant', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'nullable' => true,
			'default'  => '',
		));

	try {
		$order->save();
		echo "Columns have been added.\n";
	} catch (Exception $e) {
		echo $e->getMessage()."\n";
	}*/

		echo'<pre>';
		print_R($data);
		echo'</pre>';

	}
	}

	/* Instructions d'utilisation */
	public function usageHelp()
	{
		return <<<USAGE
Usage: 
	php -f EditDatabase.php -- [options]
				   help      This usefull help !

USAGE;
	}
}

/* Instancie + run script */
$shell = new Sturquier_EditDatabase();
$shell->run();
