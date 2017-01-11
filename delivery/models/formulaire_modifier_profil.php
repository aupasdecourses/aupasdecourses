<?php

  // Ne pas oublier d'inclure la librairie Form
  require CHEMIN_LIB.'Zebra_Form/Zebra_Form.php';
  include_once(CHEMIN_LIB . 'Zebra_Image.php');
  
  // "form_modif_infos" est l'ID unique du formulaire
  $form_modif_infos = new Zebra_Form("form_modif_infos");
  
  $flag_infos=[
    'nom_utilisateur'=>'Nom',
    'prenom_utilisateur'=>'Prénom',
    'email'=>'Email',
    'telephone'=>'Telephone'
  ];
    
  //----------------------------------------------------//
  //MODIFICATION INFOS
  //----------------------------------------------------//
    
    foreach($flag_infos as $key => $value){
       
    $label='label_'.$key;
    $message=$value.' requis!';
    
    if($key=='email'){
      $form_modif_infos->add('label', $label, $key, $value);
      $obj=$form_modif_infos->add('text', 'email',$_SESSION['email'], array('autocomplete' => 'off'));
      $obj->set_rule(array(
	'required'  =>  array('error', $value.' requis!'),
	'email'     =>  array('error', 'Votre email semble invalide!')
      ));
      $form_modif_infos->add('note', 'note_email', 'email',
      'Merci d\'entrer un email valide. Un email sera envoyé à cette adresse avec un lien que vous devrez suivre pour activer votre compte.',
      array('style'=>'width:200px')
      );
    } else {
      $form_modif_infos->add('label', $label, $key, $value);
      $obj=$form_modif_infos->add('text', $key,$_SESSION[$key]);
      $obj->set_rule(array('required'  =>  array('error', $message)));
    }
  }
  
  // "submit"
  $form_modif_infos->add('submit', 'btnsubmit', 'Modifier mes infos');	
  
  
  //----------------------------------------------------//      
  //MODIFICATION AVATAR
  //----------------------------------------------------//
  
  $form_modif_avatar = new Zebra_Form("form_modif_avatar");
  
  $form_modif_avatar->add('label', 'label_suppr_avatar', 'suppr_avatar', 'Checkbox');
  $obj = $form_modif_avatar->add('checkboxes', 'suppr_avatar[]', array('choice1'=>'Supprimer mon avatar'));
  
  
  $champ_avatar = $form_modif_avatar->add('file', 'avatar');
  $champ_avatar->set_rule(array(
		    'filetype' => array('png', 'error', 'Votre skin doit être au format PNG !'),
		    'upload' => array(DOSSIER_AVATAR, TRUE, 'error', 'Problème lors de l\'upload du fichier'),
		    'resize' => array('', AVATAR_LARGEUR_MAXI, AVATAR_HAUTEUR_MAXI, TRUE, ZEBRA_IMAGE_BOXED, 'FFFFFF', TRUE, 100, 'error', 'Problème lors du redimentionnement de l\'avatar'),
		    'filesize' => array(1048576, 'error', 'le poid de votre skin ne doit pas exéder 1Mo !'),));
  $form_modif_avatar->add('label', 'label_avatarURL', 'avatarURL', 'Votre skin, envoie par URL (facultatif)');
  $champ_avatarURL = $form_modif_avatar->add('text', 'avatarURL');
  $champ_avatarURL->set_rule(array('url' => array(FALSE, 'error', 'Format de l\'URL non valide !'),));
  
  // "submit"
  $form_modif_avatar->add('submit', 'btnsubmit', 'Modifier mon avatar');	
  
  //----------------------------------------------------//
  //MODIFICATION MOT DE PASSE
  //----------------------------------------------------//
  $form_modif_mdp = new Zebra_Form("form_modif_mdp");
  
  //Password
  $form_modif_mdp->add('label', 'label_mdp_ancien', 'mdp_ancien', 'Ancien mot de passe');
  $obj = $form_modif_mdp->add('password', 'mdp_ancien');
  $obj->set_rule(array(
    'required'  => array('error', 'Password is required!')
  ));
  
  //Password
  $form_modif_mdp->add('label', 'label_mdp', 'mdp', 'Nouveau mot de passe');
  $obj = $form_modif_mdp->add('password', 'mdp');
  $obj->set_rule(array(
    'required'  => array('error', 'Password is required!'),
    'length'    => array(6, 10, 'error', 'The password must have between 6 and 10 characters'),
  ));

  //Verif Password
  $form_modif_mdp->add('label', 'label_mdp_verif', 'mdp_verif', 'Nouveau mot de passe (vérification)');
  $obj = $form_modif_mdp->add('password', 'mdp_verif');
  $obj->set_rule(array(
      'compare' => array('mdp', 'error', 'Password not confirmed correctly!')
  ));
  
  // "submit"
  $form_modif_mdp->add('submit', 'btnsubmit', 'Modifier mot de passe');	

?>