<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {

		//Génération de la liste des increment_id des commmandes et récupération d'un tableau des données
		if(isset($_GET['date_debut'])){
			$orderid_array=get_list_orderid();
			$date_debut=$_GET['date_debut'];
			$date_fin =end_month($date_debut);
			$data=data_facturation_products($date_debut,$date_fin,"creation");
		}

		include 'views/facturation_products.phtml';
} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>