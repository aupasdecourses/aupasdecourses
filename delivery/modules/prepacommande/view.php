<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	// Affichage de la liste des commerçants
		include 'views/prepacommande.phtml';
	
} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>