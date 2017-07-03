<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	if(isset($_GET['carte'])){
		switch($_GET['carte']){
			case 'livraison':
				header('Location: modules/carte/views/map.html');
				break;
			case 'clients':
				header('Location: modules/carte/views/map_clients.html');
				break;
		}
	} else {
		// Affichage du menu des cartes
		include 'views/carte.phtml';;
	}

} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>