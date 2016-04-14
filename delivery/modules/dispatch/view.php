<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	if(isset($_GET['option'])){
		switch($_GET['option']){
			case 'listing':
				include 'views/listing.phtml';
				break;
			case 'route':
				include 'views/route.phtml';
				break;
			case 'carte':
				header('Location: modules/carte/views/map_clients.html');
				break;
		}
	} else {
		// Affichage du menu des cartes
		//include 'views/dispatch.phtml';;
	}

} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>