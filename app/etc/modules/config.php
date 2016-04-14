<?php

//Plage horaire livraison
define('HEURE_DEBUT',7);
define('HEURE_FIN',22);
$nom_jour=['Lu','Ma','Me','Je','Ve','Sa','Di'];

// Identifiants pour la base de données. Nécessaires a PDO2.
define('SQL_DSN',      'mysql:dbname=apdc_delivery;host=localhost');
define('SQL_USERNAME', 'root');
define('SQL_PASSWORD', 'password');

// Chemins à utiliser pour accéder aux vues/modeles/librairies
$module = empty($module) ? !empty($_GET['module']) ? $_GET['module'] : 'global' : $module;
define('CHEMIN_VUE',    'modules/'.$module.'/views/');
define('CHEMIN_MODELE', 'models/');
define('CHEMIN_LIB',    'libs/');
define('CHEMIN_VUE_GLOBALE', 'global/views/');
define('CHEMIN_MAGE','../');

// Configurations relatives à l'avatar
define('AVATAR_LARGEUR_MAXI', 100);
define('AVATAR_HAUTEUR_MAXI', 100);
define('DOSSIER_AVATAR', 'images/avatars/');

//mail
define('MAILCONTACT','lepetitcommisdeparis@gmail.com'); //mail contact
define('GUSER', ''); // GMail username
define('GPWD', ''); // GMail password
define('NOM_SITE', 'Tutoriel');
//protection pour le hash, vous pouvez utilisez un générateur aléatoire comme http://www.generateurdemotdepasse.com/.
define('SALT', 'Tud72sT2');

?>