<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore1 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 1e';
    protected $_rootcaturlkey = 'commercants-1e';
    protected $_codewebsite = 'apdc_1e';
    protected $_namewebsite = 'Au Pas De Courses 1e';
    protected $_namestoregroup = 'apdc_1e';
    protected $_codeboutique = 'paris1e';
    protected $_nameboutique = 'Paris 1e';
    protected $_city='Paris';
    protected $_zipcode=array('75001');
    protected $_country='FR';
    protected $_listmailchimp='Paris 1e';

    protected $_magasin = [
            'Boucher' => array("Boucherie des Moines", "Boucherie Dandelion BIO"),
            'Boulanger' => array("Boulangerie Lendemaine"),
            'Caviste' => array("Cavavin"),
            'Primeur' => array("Au Verger Fleuri"),
            'Fromager' => array("Artisans du lait"),
            'Poissonnier' => array("Au Bon Port Montmartre"),
            'Epicerie' => array('Les Papilles Gourmandes'),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore1();
$shell->run();
    