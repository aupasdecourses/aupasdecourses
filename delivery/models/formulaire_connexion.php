<?php

// Ne pas oublier d'inclure la librairie Form
require CHEMIN_LIB.'Zebra_Form/Zebra_Form.php';

// "formulaire_connexion" est l'ID unique du formulaire
$form_connexion = new Zebra_Form('formulaire_connexion');

//Login
$form_connexion->add('label', 'label_login', 'login', 'Login:');
$obj=$form_connexion->add('text', 'login');
$obj->set_rule(array('required' => array('error', 'Le Login est requis!')));

//Password
$form_connexion->add('label', 'label_password', 'mdp', 'Mot de passe');
$obj = $form_connexion->add('password', 'mdp');
$obj->set_rule(array('required'  => array('error', 'Password is required!')));

// "submit"
$form_connexion->add('submit', 'btnsubmit', 'Submit');
               
// $form_connexion->add('Checkbox', 'connexion_auto')
//                ->label("Connexion automatique");

// Pré-remplissage avec les valeurs précédemment entrées (s'il y en a)
//$form_connexion->bound($_POST);

?>