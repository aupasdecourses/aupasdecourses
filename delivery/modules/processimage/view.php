<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	if(isset($_GET['type'])){
	    if ($_GET['type'] == 'produits') {
	        include 'views/photoproduits.phtml';
	    } elseif ($_GET['type'] == 'categories') {
	        include 'views/photocatetcom.phtml';
	    } elseif ($_GET['type'] == 'commercants') {
	        include 'views/photocatetcom.phtml';
	    }
	}
} else {
    // Inclusion de la vue d'erreur en cas de non connexion
    include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
