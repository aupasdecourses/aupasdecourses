<?php

// Vérification des droits d'accès de la page
if (!utilisateur_est_connecte()) {

	// On affiche la page d'erreur comme quoi l'utilisateur doit être connecté pour voir la page
	include CHEMIN_VUE_GLOBALE.'erreur_non_connecte.php';
	
} else {
  // Suppression de toutes les variables et destruction de la session
  
  $_SESSION = array();	
  session_destroy();

  // Suppression des cookies de connexion automatique (impossible de mettre empty string!!!!!)
//   unset($_COOKIE['id']);
//   setcookie('id', '', time() - 36000); // empty value and old timestamp
//   unset($_COOKIE['connexion_auto']);
//   setcookie('connexion_auto', '', time() - 36000); // empty value and old timestamp

  include CHEMIN_VUE.'deconnexion_ok.php';
}
?>