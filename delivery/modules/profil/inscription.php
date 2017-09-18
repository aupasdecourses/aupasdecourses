<?php

// Vérification des droits d'accès de la page
if (utilisateur_est_connecte()) {

	// On affiche la page d'erreur comme quoi l'utilisateur est déjà connecté   
	include CHEMIN_VUE_GLOBALE.'accueil_'.$_SESSION['role'].'.php';
	
} else {
  // Inclusion du modele de données pour le formulaire d'inscription
  include CHEMIN_MODELE.'formulaire_inscription.php';
   
  //validate the form
    if($form_inscription->validate()){
	  
	// Tire de la documentation PHP sur <http://fr.php.net/uniqid>
	$hash_validation = md5(uniqid(rand(), true));

	//Récupération des données POST traitées
	$flag=['login','nom_utilisateur','prenom_utilisateur','email','telephone','avatarURL','mdp','role'];
	
	foreach($flag as$f){
	  ${$f}=$_POST[$f];
	}
	
	// ajouter_membre_dans_bdd() est défini dans ~/modeles/inscription.php
	$id_utilisateur = ajouter_membre_dans_bdd($login, $nom_utilisateur, $prenom_utilisateur, sha1($mdp), $email, $telephone, $hash_validation,$role);

	// Si la base de données a bien voulu ajouter l'utliisateur (pas de doublons)
	if (ctype_digit($id_utilisateur)) {

		// On transforme la chaine en entier
		$id_utilisateur = (int) $id_utilisateur;
		
		// Preparation du mail
		$message_mail = '<html><head></head><body>
		<p>Merci de vous être inscrit sur "mon site" !</p>
		<p>Veuillez cliquer sur <a href="'.$_SERVER['PHP_SELF'].'?module=membres&amp;action=valider_compte&amp;hash='.$hash_validation.'">ce lien</a> pour activer votre compte !</p>
		</body></html>';
		
		$headers_mail  = 'MIME-Version: 1.0'                           ."\r\n";
		$headers_mail .= 'Content-type: text/html; charset=utf-8'      ."\r\n";
		$headers_mail .= 'From: "Mon site" <contact@monsite.com>'      ."\r\n";
		
		// Envoi du mail
		mail($email, 'Inscription sur Commis.fr', $message_mail, $headers_mail);
		
		// Redimensionnement et sauvegarde de l'avatar (eventuel) dans le bon dossier
      // 	  if (!empty($avatarURL)) {
      // 
      // 		  // On souhaite utiliser la librairie Image
      // 		  include CHEMIN_LIB.'image.php';
      // 	  
      // 		  // Redimensionnement et sauvegarde de l'avatar
      // 		  $avatar = new Image($avatar);
      // 		  $avatar->resize_to(100, 100); // Image->resize_to($largeur_maxi, $hauteur_maxi)
      // 		  $avatar_filename = 'images/avatar/'.$id_utilisateur .'.'.strtolower(pathinfo($avatar->get_filename(), PATHINFO_EXTENSION));
      // 		  $avatar->save_as($avatar_filename);
      // 		  
      // 		  // Mise à jour de l'avatar dans la table
      // 		  // maj_avatar_membre() est défini dans ~/modeles/membres.php
      // 		  maj_avatar_membre($id_utilisateur , $avatar_filename);
      // 
      // 	  }
		
		// Affichage de la confirmation de l'inscription
		include CHEMIN_VUE.'inscription_effectuee.php';

	// Gestion des doublons
	} else {

		// Changement de nom de variable (plus lisible)
		$erreur =& $id_utilisateur['membres'];
		
		// On vérifie que l'erreur concerne bien un doublon
		if (23000 == $erreur[0]) { // Le code d'erreur 23000 siginife "doublon" dans le standard ANSI SQL
			$pattern="`Duplicate entry '(.+)' for key '\w+'`is";
			preg_match($pattern, $erreur[2], $valeur_probleme);
			
			var_dump($valeur_probleme);
			var_dump($_POST);
			
			if ($login == $valeur_probleme[1]) {
			
				$erreurs_inscription[] = "Ce nom d'utilisateur est déjà utilisé.";
			
			} else if ($email == $valeur_probleme[1]) {
			
				$erreurs_inscription[] = "Cette adresse e-mail est déjà utilisée.";
			
			} else {
			
				$erreurs_inscription[] = "Erreur ajout SQL : doublon non identifié présent dans la base de données.";
			}
		
		} else {
		
			$erreurs_inscription[] = sprintf("Erreur ajout SQL : cas non traité (SQLSTATE = %d).", $erreur[0]);
		}
		
		// On reaffiche le formulaire d'inscription
		include CHEMIN_VUE.'formulaire_inscription.php';
	  }  
    } else{
    
    // Inclusion du modele de données pour le formulaire d'inscription
	include CHEMIN_VUE.'formulaire_inscription.php';
      } 
}
?>