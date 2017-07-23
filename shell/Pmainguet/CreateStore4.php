<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore4 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 4e';
    protected $_rootcaturlkey = 'commercants-4e';
    protected $_codewebsite = 'apdc_4e';
    protected $_namewebsite = 'Au Pas De Courses 4e';
    protected $_namestoregroup = 'apdc_4e';
    protected $_codeboutique = 'paris4e';
    protected $_nameboutique = 'Paris 4e';
    protected $_city='Paris';
    protected $_zipcode=array('75004');
    protected $_country='FR';
    protected $_listmailchimp='Paris 4e';

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
$shell = new Pmainguet_CreateStore4();
$shell->run();
    