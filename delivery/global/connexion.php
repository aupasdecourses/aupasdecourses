<?php

// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {
	// On affiche la page d'accueil en fonction du role   
	include CHEMIN_VUE_GLOBALE.'/accueil.phtml';
	
	
} else {
  // Inclusion du modele de données pour le formulaire de connexion
  include CHEMIN_MODELE.'formulaire_connexion.php';

  //validate the form
  if($form_connexion->validate()){

      // Création d'un tableau des erreurs
      $erreurs_connexion = array();
      
      //Récupération des données POST traitées
      $flag=['login','mdp'];
      
      foreach($flag as$f){
	${$f}=$_POST[$f];
      }
      
       // combinaison_connexion_valide() est définit dans ~/modeles/membres.php
      $id_utilisateur = combinaison_connexion_valide($login, sha1($mdp));
      
      // Si les identifiants sont valides
      if (false !== $id_utilisateur) {

	      $infos_utilisateur = lire_infos_utilisateur($id_utilisateur);
	      
	      $flag=['mail','nom_utilisateur','telephone','role'];
	      
	      // On enregistre les informations dans la session
	      $_SESSION['id']     = $id_utilisateur;
	      $_SESSION['login'] = $login;
	      
	      foreach($flag as $f){
		$_SESSION[$f] = $infos_utilisateur[$f];
	      }
		      // On affiche la page d'accueil   
		      include CHEMIN_VUE_GLOBALE.'/accueil.phtml';
      
      } else {

	      $erreurs_connexion[] = "Couple nom d'utilisateur / mot de passe inexistant.";
	      	      
	      // On réaffiche le formulaire de connexion
	      include CHEMIN_VUE_GLOBALE.'formulaire_connexion.phtml';
      }
    
  } else{
    // On réaffiche le formulaire de connexion
    include CHEMIN_VUE_GLOBALE.'formulaire_connexion.phtml';
  }
}

?>