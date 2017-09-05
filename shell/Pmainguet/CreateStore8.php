<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore8 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 8e';
    protected $_rootcaturlkey = 'commercants-8e';
    protected $_codewebsite = 'apdc_8e';
    protected $_namewebsite = 'Au Pas De Courses 8e';
    protected $_namestoregroup = 'apdc_8e';
    protected $_codeboutique = 'paris8e';
    protected $_nameboutique = 'Paris 8e';
    protected $_city='Paris';
    protected $_zipcode=array('75008');
    protected $_country='FR';
    protected $_listmailchimp='Paris 8e';

    protected $_magasin = [
            'Boucher' => array("Boucherie des Moines", "Boucherie Dandelion BIO"),
            'Boulanger' => array("Boulangerie Lendemaine"),
            'Caviste' => array("Bouteille"),
            'Primeur' => array("Les Fruits de la Terre BIO","Pascal Bassard Primeur"),
            'Fromager' => array("Artisans du lait","Les Fromages des Batignolles"),
            'Poissonnier' => array("Marée 17"),
            'Epicerie' => array('Le Garde Manger des Dames', 'Terres de Café','Laurent Roy Chocolatier'),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore8();
$shell->run();
    