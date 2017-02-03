<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	// Affichage de la liste des commerçants
	if(isset($_GET['id'])){
		include 'views/profil_commercant.phtml';		
	} else {
		if($_GET['option']=='order'){
			include 'views/list_commercant_order.phtml';
		}elseif($_GET['option']=='profile'){
			include 'views/list_commercant_profil.phtml';
		}else{
			include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
		}
	}
} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>