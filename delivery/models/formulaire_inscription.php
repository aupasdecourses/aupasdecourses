<?php

    // Ne pas oublier d'inclure la librarie Form
    require CHEMIN_LIB.'Zebra_Form/Zebra_Form.php';
    include_once(CHEMIN_LIB . 'Zebra_Image.php');


    // "formulaire_inscription" est l'ID unique du formulaire
    $form_inscription = new Zebra_Form('formulaire_inscription','post', '', array('class'=>'form','autocomplete' => 'off'));

    // Role
    $form_inscription->add('label', 'label_role', 'role', 'Role:');
    $obj=$form_inscription->add('select', 'role');
    $obj->set_rule(array('required' => array('error', 'Le rôle est requis!')));
    $obj->add_options(array('livreur'=>'Livreur','commercant'=>'Commerçant'));

    // Login
    $form_inscription->add('label', 'label_login', 'login', 'Login:');
    $obj=$form_inscription->add('text', 'login');
    $obj->set_rule(array('required'  =>  array('error', 'Un login est requis!')));

    // Nom
    $form_inscription->add('label', 'label_nom', 'nom_utilisateur', 'Nom:');
    $obj=$form_inscription->add('text', 'nom_utilisateur');
    $obj->set_rule(array('required'  =>  array('error', 'Votre nom est requis!')));

  // Prénom
    $form_inscription->add('label', 'label_prenom', 'prenom_utilisateur', 'Prénom:');
    $obj=$form_inscription->add('text', 'prenom_utilisateur');
    $obj->set_rule(array('required'  =>  array('error', 'Votre prénom est requis!')));

    //Email
    $form_inscription->add('label', 'label_email', 'email', 'Email');
    $obj=$form_inscription->add('text', 'email', '', array('autocomplete' => 'off'));
    $obj->set_rule(array(
      'required'  =>  array('error', 'Votre email est requis!'),
      'email'     =>  array('error', 'Votre email semble invalide!'),
    ));
    $form_inscription->add('note', 'note_email', 'email',
      'Merci d\'entrer un email valide. Un email sera envoyé à cette adresse avec un lien que vous devrez suivre pour activer votre compte.',
      array('style'=>'width:200px')
    );

 // Telephone
    $form_inscription->add('label', 'label_telephone', 'telephone', 'Téléphone:');
    $obj = $form_inscription->add('text', 'telephone');
    $obj->set_rule(array(
        // error messages will be sent to a variable called "error", usable in custom templates
        'required'  =>  array('error', 'Un téléphone est requis!'),
    ));
    
  //Photo
	$champ_avatar = $form_inscription->add('file', 'avatar');
	$champ_avatar->set_rule(array(
			  'filetype' => array('png', 'error', 'Votre photo doit être au format PNG !'),
			  'upload' => array(DOSSIER_AVATAR, TRUE, 'error', 'Problème lors de l\'upload du fichier'),
			  'resize' => array('', AVATAR_LARGEUR_MAXI, AVATAR_HAUTEUR_MAXI, TRUE, ZEBRA_IMAGE_BOXED, 'FFFFFF', TRUE, 100, 'error', 'Problème lors du redimentionnement de l\'avatar'),
			  'filesize' => array(1048576, 'error', 'le poid de votre skin ne doit pas exéder 1Mo !'),));
	$form_inscription->add('label', 'label_avatarURL', 'avatarURL', 'Votre photo (à partir de votre ordinateur ou via URL (facultatif)');
	$champ_avatarURL = $form_inscription->add('text', 'avatarURL');
	$champ_avatarURL->set_rule(array('url' => array(FALSE, 'error', 'Format de l\'URL non valide !'),));
    
    //Password
    $form_inscription->add('label', 'label_password', 'mdp', 'Mot de passe');
    $obj = $form_inscription->add('password', 'mdp');
    $obj->set_rule(array(
      'required'  => array('error', 'Password is required!'),
      'length'    => array(6, 10, 'error', 'The password must have between 6 and 10 characters'),
    ));
    $form_inscription->add('note', 'note_password', 'mdp', 'Password must be have between 6 and 10 characters.', array('style' => 'width: 180px'));

    //Verif Password
    $form_inscription->add('label', 'label_confirm_password', 'mdp_verif', 'Mot de passe (vérification)');
    $obj = $form_inscription->add('password', 'mdp_verif');
    $obj->set_rule(array(
        'compare' => array('mdp', 'error', 'Password not confirmed correctly!')
    ));
    
    // "captcha"
    $form_inscription->add('captcha', 'captcha_image', 'captcha_code');
    $form_inscription->add('label', 'label_captcha_code', 'captcha_code', 'Are you human?');
    $obj = $form_inscription->add('text', 'captcha_code');
    $form_inscription->add('note', 'note_captcha', 'captcha_code', 'You must enter the characters with black color that stand
    out from the other characters', array('style'=>'width: 200px'));
    $obj->set_rule(array(
      'required'  => array('error', 'Entrez les caractères de l\'image ci-dessus!'),
      'captcha' => array('error', 'Les caractères entrés ne correspondent pas à ceux de l\'image')
    ));

    // "submit"
    $form_inscription->add('submit', 'btnsubmit', 'Submit');
    
    // Pré-remplissage avec les valeurs précédemment entrées (s'il y en a)
    //$form_inscription->bound($_POST);
    
?>