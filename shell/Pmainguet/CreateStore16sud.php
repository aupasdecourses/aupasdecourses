<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore16sud extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 16e Sud';
    protected $_rootcaturlkey = 'commercants-16e-sud';
    protected $_codewebsite = 'apdc_16e_sud';
    protected $_namewebsite = 'Au Pas De Courses 16e Sud';
    protected $_namestoregroup = 'apdc_16e_sud';
    protected $_codeboutique = 'paris16esud';
    protected $_nameboutique = 'Paris 16e Sud';
    protected $_city='Paris';
    protected $_zipcode=array('75016');
    protected $_country='FR';
    protected $_listmailchimp='Paris 16e';

    protected $_magasin = [
            'Boucher' => array(" Milo"),
            'Boulanger' => array(""),
            'Caviste' => array(""),
            'Primeur' => array(""),
            'Fromager' => array(""),
            'Poissonnier' => array(""),
            'Epicerie' => array(''),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore16sud();
$shell->run();
    