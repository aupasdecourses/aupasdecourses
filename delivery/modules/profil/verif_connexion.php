<?php
// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {

	// On affiche la page d'erreur comme quoi l'utilisateur est déjà connecté   
	include CHEMIN_VUE.'connexion_ok.php';
	
} else {

  // Création d'un tableau des erreurs
  $erreurs_connexion = array();

  // Validation des champs suivant les règles
  if ($form_connexion->is_valid($_POST)) {
	  
	  list($nom_utilisateur, $mot_de_passe) =
		  $form_connexion->get_cleaned_data('nom_utilisateur', 'mot_de_passe');
	  
	  // combinaison_connexion_valide() est définit dans ~/modeles/membres.php
	  $id_utilisateur = combinaison_connexion_valide($nom_utilisateur, sha1($mot_de_passe));
	  
	  // Si les identifiants sont valides
	  if (false !== $id_utilisateur) {

		  $infos_utilisateur = lire_infos_utilisateur($id_utilisateur);
		  
		  // On enregistre les informations dans la session
		  $_SESSION['id']     = $id_utilisateur;
		  $_SESSION['pseudo'] = $nom_utilisateur;
		  $_SESSION['avatar'] = $infos_utilisateur['avatar'];
		  $_SESSION['email']  = $infos_utilisateur['adresse_email'];
		  
		  // Mise en place des cookies de connexion automatique
		  if (!empty($form_connexion->get_cleaned_data('connexion_auto')))
		  {
			  $navigateur = (!empty($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
			  $hash_cookie = sha1('helloworld'.$nom_utilisateur.'foxtrotbravo'.sha1($mot_de_passe).'biloubilou'.$navigateur.'zobilamouche');
		      
			  setcookie('id',$_SESSION['id'], strtotime("+1 year"), '/');
			  setcookie('connexion_auto', $hash_cookie,    strtotime("+1 year"), '/');
		  }
		  
		  // Affichage de la confirmation de la connexion
		  include CHEMIN_VUE.'connexion_ok.php';
	  
	  } else {

		  $erreurs_connexion[] = "Couple nom d'utilisateur / mot de passe inexistant.";
		  
		  // Suppression des cookies de connexion automatique
		  setcookie('id', '');
		  setcookie('connexion_auto', '');
		  
		  // On réaffiche le formulaire de connexion
		  include CHEMIN_VUE.'formulaire_connexion.php';
	  }	  
  } else {
		// On réaffiche le formulaire de connexion
		include CHEMIN_VUE.'formulaire_connexion.php';
	}
}
?>
