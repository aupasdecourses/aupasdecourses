<?php

//Plage horaire livraison
define('HEURE_DEBUT',7);
define('HEURE_FIN',22);
$nom_jour=['Lu','Ma','Me','Je','Ve','Sa','Di'];

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
define('MAILCONTACT','contact@aupasdecourses.com'); //mail contact
define('GUSER', ''); // GMail username
define('GPWD', ''); // GMail password
define('NOM_SITE', 'Tutoriel');
//protection pour le hash, vous pouvez utilisez un générateur aléatoire comme http://www.generateurdemotdepasse.com/.
define('SALT', 'Tud72sT2');

//Define date to switch between Amasty and MW Date Delivery Modules
define('AMASTY_MW_DATE',date("Y-m-d", mktime(0, 0, 0, 1, 20, 2016)));

//Define type of orders to display based on status (global variables)
$GLOBALS['ORDER_STATUS_NODISPLAY']=array('pending', 'pending_payment','payment_review','holded','closed','canceled');
$GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']=2016000249;

?>