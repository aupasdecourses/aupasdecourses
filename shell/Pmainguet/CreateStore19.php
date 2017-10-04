<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore19 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 19e';
    protected $_rootcaturlkey = 'commercants-19e';
    protected $_codewebsite = 'apdc_19e';
    protected $_namewebsite = 'Au Pas De Courses 19e';
    protected $_namestoregroup = 'apdc_19e';
    protected $_codeboutique = 'paris19e';
    protected $_nameboutique = 'Paris 19e';
    protected $_city='Paris';
    protected $_zipcode=array('75019');
    protected $_country='FR';
    protected $_listmailchimp='Paris 19e';

    protected $_magasin = [
            'Boucher' => array("Boucherie Lévêque"),
            'Boulanger' => array("La Miche Qui Fume"),
            'Caviste' => array("La cave du marché Saint-Martin"),
            'Primeur' => array("Verger Saint-Martin"),
            'Fromager' => array("Fromagerie Bouvet"),
            'Poissonnier' => array("Les Viviers de Noirmoutier"),
            'Epicerie' => array(''),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore19();
$shell->run();
    