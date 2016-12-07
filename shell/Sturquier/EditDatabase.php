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

	private $comment_client;
	private $comment_commercant;

	/*public function editamastytable()*/
	public function run()
	{
		echo"// Edition de la table amasty_amorderattach_order_field ////\n\n";
		$order = Mage::getModel('amorderattach/order_field');
		$orders = $order->getCollection();

		foreach($orders as $ord)
		{
			$data = $ord->getData();

			try
			{
				$data['commentaires_client']		= $comment_client;
				$data['commentaires_commercant']	= $comment_commercant;

				$data['commentaires_commande']		= $data['commentaires_commande'].'//'.$data['commentaires_fraislivraison'];
				$data['remboursements']				= $data['remboursements'].'//'.$data['commentaires_ticket'];

				$data['commentaires_client']		= $data['commentaires_commande'];
				$data['commentaires_commercant']	= $data['remboursements'];

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
		echo"// La table amasty_amorderattach_order_field a été correctement MAJ ////\n\n";
	}
/* Steps ne fonctionne pas pour UNE seule fonction ?? */
/*
	public function run()
	{
		$steps = ['editamastytable'];
		$step = $this->getArg('step');
		if (in_array($step, $steps))
		{
			$this->$step();
		} else 
		{
			echo"Step must be one of these:\n";
			foreach ($steps as $s)
			{
				echo $s.",\n";
			}
		}
	}
 */
	public function usageHelp()
	{
		return <<<USAGE
Usage: 
	php -f EditDatabase.php -- [options]
				   help      This usefull help !

USAGE;
	}

}

$shell = new Sturquier_EditDatabase();
$shell->run();
