<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore6 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 6e';
    protected $_rootcaturlkey = 'commercants-6e';
    protected $_codewebsite = 'apdc_6e';
    protected $_namewebsite = 'Au Pas De Courses 6e';
    protected $_namestoregroup = 'apdc_6e';
    protected $_codeboutique = 'paris6e';
    protected $_nameboutique = 'Paris 6e';
    protected $_city='Paris';
    protected $_zipcode=array('75006');
    protected $_country='FR';
    protected $_listmailchimp='Paris 6e';

    protected $_magasin = [
            'Boucher' => array(""),
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
$shell = new Pmainguet_CreateStore6();
$shell->run();
    