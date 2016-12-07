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

		foreach($orders as $ord)
		{
			$data = $ord->getData();

			try 
			{
				$data['commentaires_client'] = $comment_client;
				$data['commentaires_commercant'] = $comment_commercant;

				$data['commentaires_commande'] = $data['commentaires_commande'].$data['commentaires_fraislivraison'];
				$data['remboursements'] = $data['remboursements'].$data['commentaires_ticket'];	

				$data['commentaires_client'] = $data['commentaires_commande'];
				$data['commentaires_commercant'] = $data['remboursements'];

				unset($data['ticket_commercant']);
				unset($data['commentaires_commande']);
				unset($data['commentaires_fraislivraison']);
				unset($data['remboursements']);
				unset($data['commentaires_ticket']);

				$new_orders = Mage::getModel('amorderattach/order_field')->setData($data);
				$new_orders->save();
			} catch (Exception $e) 
			{
				echo $e->getMessage()."\n";
			}
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
