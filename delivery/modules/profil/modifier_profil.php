<?php

if (utilisateur_est_connecte()) {
    
    // Inclusion du modele de données pour le formulaire d'inscription
    include CHEMIN_MODELE.'formulaire_modifier_profil.php';
  
  // lire_infos_utilisateur() est défini dans ~/modules/membres.php
  $infos_utilisateur = lire_infos_utilisateur($_SESSION['id']);
  
  // Si le profil existe et que le compte est validé
  if (false !== $infos_utilisateur && $infos_utilisateur['hash_validation'] == '') {

	  // Création des tableaux des erreurs (un par formulaire)
	  $erreurs_form_modif_infos = array();
	  $erreurs_form_modif_avatar = array();
	  $erreurs_form_modif_mdp   = array();
	  
	  // et d'un tableau des messages de confirmation
	  $msg_confirm = array();
    
	  //----------------------------------------------------//
	  //MODIFICATION INFOS
	  //----------------------------------------------------//
	  
	  if ($form_modif_infos->validate()) {

		  
		  //flag_infos est dans formulaire_modifier_profils
		  foreach($flag_infos as $f=>$value){
		    if(isset($_POST[$f])){${$f}=$_POST[$f];}

		    // modification de l'info si non vide
		    if (!empty(${$f})) {
	    
			    $test = maj_infos_utilisateur($f,$_SESSION['id'], ${$f});
	    
			    if (true === $test) {
	    
				    // Ça a marché, trop cool !
				    $_SESSION[$f]=${$f};
				    $msg_confirm[] = $value." mis à jour avec succès !";
	    
			    // Gestion des doublons
			    } else {
	    
				    // Changement de nom de variable (plus lisible)
				    $erreur =& $test;
	    
				    // On vérifie que l'erreur concerne bien un doublon
				    if (23000 == $erreur[0]) { // Le code d'erreur 23000 signifie "doublon" dans le standard ANSI SQL
	    
					    preg_match("`Duplicate entry '(.+)' for key \w+`is", $erreur[2], $valeur_probleme);
					    $valeur_probleme = $valeur_probleme[1];
	    
					    if ($adresse_email == $valeur_probleme) {
	    
						    $erreurs_form_modif_infos[] = $value." déjà utilisé(e).";
	    
					    } else {
	    
						    $erreurs_form_modif_infos[] = "Erreur ajout SQL : doublon non identifié présent dans la base de données.";
					    }
	    
				    } else {
	    
					    $erreurs_form_modif_infos[] = sprintf("Erreur ajout SQL : cas non traité (SQLSTATE = %d).", $erreur[0]);
				    }
	    
			    }
		    }
	  
		  //End foreach
		  }
	  
	//----------------------------------------------------//
	//MODIFICATION AVATAR
	//----------------------------------------------------//
	
	} else if ($form_modif_avatar->validate()) {	  
		  
		  // Si l'utilisateur veut supprimer son avatar...
		  if (!empty($suppr_avatar)) {
	  
			  maj_avatar_membre($_SESSION['id'], '');
			  $_SESSION['avatarURL'] = '';
	  
			  $msg_confirm[] = "Avatar supprimé avec succès !";
	  
		  // ... ou le modifier !
		  } else if (!empty($avatarURL)) {
	  
			  // On souhaite utiliser la librairie Image
			  include CHEMIN_LIB.'image.php';
	  
			  // Redimensionnement et sauvegarde de l'avatar
			  $avatarURL = new Image($avatarURL);
			  $avatar->resize_to(100, 100); // Image->resize_to($largeur_maxi, $hauteur_maxi)
			  $avatar_filename = DOSSIER_AVATAR.$id_utilisateur .'.'.strtlower(pathinfo($avatarURL->get_filename(), PATHINFO_EXTENSION));
			  $avatar->save_as($avatar_filename);
	  
			  // On veut utiliser le modèle des membres (~/modules/membres.php)
			  include CHEMIN_MODELE.'membres.php';
	  
			  // Mise à jour de l'avatar dans la table
			  // maj_avatar_membre() est définit dans ~/modules/membres.php
			  maj_avatar_membre($_SESSION['id'] , $avatar_filename);
			  $_SESSION['avatarURL'] = $avatar_filename;
	  
			  $msg_confirm[] = "Avatar modifié avec succès !";
		  }
	  
	  //----------------------------------------------------//
	  //MODIFICATION MOT DE PASSE
	  //----------------------------------------------------//
	  
	  } else if ($form_modif_mdp->validate()) {
	  
		  // On vérifie si les 2 mots de passe correspondent
		  if ($_POST['mdp'] != $_POST['mdp_verif']) {
	  
			  $erreurs_form_modif_mdp[] = "Les deux mots de passes entrés sont différents !";
	  
		  // C'est bon, on peut modifier la valeur dans la BDD
		  } else {
	  
		  $mdp = $_POST['mdp'];

			  maj_mot_de_passe_membre($_SESSION['id'],$mdp);			
	  
			  $msg_confirm[] = "Votre mot de passe a été modifié avec succès !";
		  }
	  
	  }
	  
	  include CHEMIN_VUE.'formulaire_modifier_profil.php';
	  
  } else {
      include CHEMIN_VUE.'erreur_profil_inexistant.php';
    }
} else{
    include CHEMIN_VUE_GLOBALE.'erreur_non_connecte.php';
}
?>