<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
		
		// Affichage de la calculette
		$orders=Mage::getModel('sales/order')->getCollection();

		//récupération de la liste des commandes (numéro et date)
		$orderid_array=get_list_orderid();
		krsort($orderid_array);

		//Récupération des données de la commande si spécifié
		if(isset($_GET['increment_id'])){
			$select_id=$_GET['increment_id'];			
			$order=Mage::getModel('sales/order')->loadByIncrementId($select_id);
			$orderid=$order->getId();
			$orderstatus=$order->getStatusLabel();

			//Vérifie si on n'a pas déjà entré d'infos dans Order Attachments
			$check=Mage::getModel('amorderattach/order_field')->getCollection()->addFieldToFilter('order_id', $orderid)->getFirstItem()->getId();
		}else{
			$select_id='';
		}

		//Load additionnal data if data already exists
		if ($check!=NULL || $check!=""){
			$collection_attach=Mage::getModel('amorderattach/order_field')->getCollection()->addFieldToFilter('order_id', $orderid);
			$model_order=Mage::getSingleton('pmainguet_delivery/refund_order');
		}

		include 'views/calculette.phtml';	

} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>