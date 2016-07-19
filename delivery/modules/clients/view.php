<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	$index=['fidelity'=>'views/clients_fidelity.phtml',
		'stat'=>'views/clients_stat.phtml',
		'coupon'=>'views/clients_coupon.phtml'
	];
	if(array_key_exists($_GET['option'], $index)){
		include $index[$_GET['option']];
	}	
} else {
  // Inclusion de la vue d'erreur en cas de non connexion
  include CHEMIN_VUE_GLOBALE.'erreur_connexion.phtml';
}
?>