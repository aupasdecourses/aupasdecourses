<?php

/**
 * Classe implémentant le singleton pour PDO
 * @author Savageman
 * http://sdz.tdct.org/sdz/votre-site-php-presque-complet-architecture-mvc-et-bonnes-pratiques.html
 */

class PDO2 extends PDO {

	private static $_instance;

	/* Constructeur : héritage public obligatoire par héritage de PDO */
	public function __construct( ) {
	
	}
	// End of PDO2::__construct() */

	/* Singleton
	    Design Pattern permettant de ne créer qu'un et un seul objet
	*/
	public static function getInstance() {
	
		if (!isset(self::$_instance)) {
			
			try {
				//permet d'avoir une constante $_instance constante pour tous les objets créés avec cette classe, via l'opérateur
				//de résolution de portée "::"
				self::$_instance = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
			
			} catch (PDOException $e) {
			
				echo $e;
			}
		} 
		return self::$_instance; 
	}
	// End of PDO2::getInstance() */
}

// end of file */
?>