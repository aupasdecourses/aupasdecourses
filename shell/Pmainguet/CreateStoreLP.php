<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStoreLP extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants Levallois-Perret';
    protected $_rootcaturlkey = 'commercants-levallois-perret';
    protected $_codewebsite = 'apdc_levallois_perret';
    protected $_namewebsite = 'Au Pas De Courses Levallois-Perret';
    protected $_namestoregroup = 'apdc_levallois_perret';
    protected $_codeboutique = 'levalloisperret';
    protected $_nameboutique = 'Levallois-Perret';
    protected $_city='Levallois-Perret';
    protected $_zipcode=array('92300','75017','92200');
    protected $_country='FR';
    protected $_listmailchimp='Levallois Perret';

    protected $_contacts = array(
            "Boucherie Cacher Julien & Fils" => [
                'firstname'=>'Philipe',
                'lastname'=>'Fargeon',
                'email'=>'phillipefargeon68@gmail.com',
                'phone'=>'01 41 05 05 55',
            ],
            "L'Epicerie Quotidienne" => [
                'firstname'=>'Nicolas',
                'lastname'=>'Ruoppolo',
                'email'=>'contact@lepiceriequotidienne.fr',
                'phone'=>'06 22 60 68 54',
            ],
            "Le Verger de Levallois" => [
                'firstname'=>'Contact',
                'lastname'=>'Commande Le Verger de Levallois',
                'email'=>'contact@achanger.com',
                'phone'=>'',
            ],
        );

    protected $_commercant = [
            "Boucherie Cacher Julien & Fils"=>array('name'=>'BOUCHERIE CACHER JULIEN & FILS'),
            "L'Epicerie Quotidienne"=>array('name'=>'EPICERIE QUOTIDIENNE'),
            "Le Verger de Levallois"=>array('name'=>'LE VERGER DE LEVALLOIS'),
            ];

    protected $_magasin = [
            'Primeur' => array("Le Verger de Levallois"),
            'Boucher' => array("Boucherie Cacher Julien & Fils","Boucherie Meissonier"),
            'Fromager' => array(""),
            'Poissonnier' => array(""),
            'Caviste' => array(""),
            'Boulanger' => array(""),
            'Epicerie' => array("L'Epicerie Quotidienne"),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

    protected $_googlesheets = [
            "Boucherie Cacher Julien & Fils"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1kx5MzXEcDiUl0S-Ur_kKC_BDzm9OqVDZATag3ZXDi5w',
                'street'=>'3 Place du Maréchal de Lattre de Tassigny',
                'postcode'=>'92300',
                'phone'=>'',
                'code'=>'BCJ92',
                'timetable'=>array("8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00",""),
            ),
            "L'Epicerie Quotidienne"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1oTD8lg3sW5VDyS9mdKs8pxuerfbWfBZj1oMeV8T3PmI',
                'street'=>'40 rue Louise Michel',
                'postcode'=>'92300',
                'phone'=>'',
                'code'=>'LEQ92',
                'timetable'=>array("10:30–15:00","10:30–15:00/16:30–20:00","10:30–15:00/16:30–20:00","10:30–15:00/16:30–20:00","10:30–15:00/16:30–20:00","10:30–15:00/16:30–20:00",""),
            ),
            "Le Verger de Levallois"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1oTD8lg3sW5VDyS9mdKs8pxuerfbWfBZj1oMeV8T3PmI',
                'street'=>'14 rue Louise Michel',
                'postcode'=>'92300',
                'phone'=>'',
                'code'=>'LVL92',
                'timetable'=>array("10:00-20:00","10:00-20:00","10:00-20:00","10:00-20:00","10:00-20:00","10:00-20:00",""),
            )];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStoreLP();
$shell->run();
    