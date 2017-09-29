<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore20 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 20e';
    protected $_rootcaturlkey = 'commercants-20e';
    protected $_codewebsite = 'apdc_20e';
    protected $_namewebsite = 'Au Pas De Courses 20e';
    protected $_namestoregroup = 'apdc_20e';
    protected $_codeboutique = 'paris20e';
    protected $_nameboutique = 'Paris 20e';
    protected $_city='Paris';
    protected $_zipcode=array('75020');
    protected $_country='FR';
    protected $_listmailchimp='Paris 20e';

    protected $_magasin = [
            'Boucher' => array("La Boucherie Royale"),
            'Boulanger' => array("Boulangerie Dupain"),
            'Caviste' => array("Paris Terroirs"),
            'Primeur' => array("Indé Bio"),
            'Fromager' => array("Fromagerie Goncourt"),
            'Poissonnier' => array("Poissonnerie Collachot"),
            'Epicerie' => array(''),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore20();
$shell->run();
    