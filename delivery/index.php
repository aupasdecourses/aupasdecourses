<?php
//Source and inspiration: http://sdz.tdct.org/sdz/votre-site-php-presque-complet-architecture-mvc-et-bonnes-pratiques.html

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialisation
include 'global/init.php';


// Début de la temporisation de sortie
ob_start();

//Connection à Magento
include CHEMIN_MODELE.'magento.php';
connect_magento();

// Identifiants pour la base de données. Nécessaires a PDO2.
//Config OVH

$config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");
$_host = $config->host;
$_uname = $config->username;
$_pass = $config->password;
$_dbname = $config->dbname;

define('SQL_DSN', 'mysql:dbname='.$_dbname.';host='.$_host);
define('SQL_USERNAME', $_uname);
define('SQL_PASSWORD', $_pass);

// Si un module est specifié, on regarde s'il existe
if (!empty($_GET['module'])) {

	$module = dirname(__FILE__).'/modules/'.$_GET['module'].'/';
	
	// Si l'action est specifiée, on l'utilise, sinon, on tente une action par défaut
	$action = (!empty($_GET['action'])) ? $_GET['action'].'.php' : 'index.php';
	
	// Si l'action existe, on l'exécute
	if (is_file($module.$action)) {

		include $module.$action;

	// Sinon, on affiche la page d'accueil !
	} else {
		include CHEMIN_VUE_GLOBALE.'/accueil.php';
	}

// Module non specifié ou invalide ? On affiche la page d'accueil !
} else {
	if(utilisateur_est_connecte()){
	  include CHEMIN_VUE_GLOBALE.'/accueil.phtml';
	}else{
	  include 'global/connexion.php';
	}

}

// Fin de la temporisation de sortie
$contenu = ob_get_clean();

// Début du code HTML
include 'global/header.php';

echo $contenu;

// Fin du code HTML
include 'global/footer.php';
?>