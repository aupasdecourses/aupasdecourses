<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	// Affichage de la liste des commerçants
	if(isset($_GET['id'])){
		include 'views/commande_commercant.phtml';		
	} elseif($_GET['option']=='client'){
		$orders=liste_commande();
		include 'views/liste_commande_client.phtml';
	} else {
		$orders=liste_commande();
		include 'views/liste_commande.phtml';
	}
	
} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>