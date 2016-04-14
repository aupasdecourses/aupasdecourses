<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	// Affichage de la liste des commerçants
	if($_GET['option']=='fidelity'){
		include 'views/clients_fidelity.phtml';		
	} elseif($_GET['option']=='stat') {
		//$orders=liste_commande();
		include 'views/clients_stat.phtml';
	}
	
} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>