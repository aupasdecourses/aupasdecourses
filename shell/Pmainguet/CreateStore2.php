<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore2 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 2e';
    protected $_rootcaturlkey = 'commercants-2e';
    protected $_codewebsite = 'apdc_2e';
    protected $_namewebsite = 'Au Pas De Courses 2e';
    protected $_namestoregroup = 'apdc_2e';
    protected $_codeboutique = 'paris2e';
    protected $_nameboutique = 'Paris 2e';
    protected $_city='Paris';
    protected $_zipcode=array('75002');
    protected $_country='FR';
    protected $_listmailchimp='Paris 2e';

    protected $_magasin = [
            'Boucher' => array("Boucherie Milo"),
            'Boulanger' => array("Boulangerie Dupain"),
            'Caviste' => array("Paris Terroirs"),
            'Primeur' => array("Chez Wagner Primeur Bio"),
            'Fromager' => array("La Petite Ferme d'Inès"),
            'Poissonnier' => array("La Cabane du Pêcheur"),
            'Epicerie' => array('Terres de Café Blancs Manteaux'),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore2();
$shell->run();
    