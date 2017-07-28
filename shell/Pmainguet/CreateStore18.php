<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore18 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 18e';
    protected $_rootcaturlkey = 'commercants-18e';
    protected $_codewebsite = 'apdc_18e';
    protected $_namewebsite = 'Au Pas De Courses 18e';
    protected $_namestoregroup = 'apdc_18e';
    protected $_codeboutique = 'paris18e';
    protected $_nameboutique = 'Paris 18e';
    protected $_city='Paris';
    protected $_zipcode=array('75018');
    protected $_country='FR';
    protected $_listmailchimp='Paris 18e';

    protected $_contacts = array(
            "Aux Viandes de Montmartre" => [
                'firstname'=>'Joseph',
                'lastname'=>'Kowalczyk',
                'email'=>'auxviandesdemontmartre@gmail.com',
                'phone'=>'06 18 71 62 18',
            ],
            "Poissonnerie du Dôme Damrémont"=>[
                'firstname'=>'Sebastien ',
                'lastname'=>'XXX',
                'email'=>'pdd18apdc@gmail.com',
            ],
            "Délicate & Saine"=>[
                'firstname'=>'Martin',
                'lastname'=>'Le Roy',
                'email'=>'martinleroy19@gmail.com',
            ],
            "Poèmes des 4 saisons"=>[
                'firstname'=>'Foued',
                'lastname'=>'Karouia',
                'email'=>'poemesdes4saisons@gmail.com',
            ]);

    protected $_commercant = [
            "Aux Viandes de Montmartre"=>array('name'=>'AUX VIANDES DE MONTMARTRE'),
            "Poissonnerie du Dôme Damrémont"=>array('name'=>"POISSONNERIE DU DOME"),
            "Délicate & Saine"=>array('name'=>"DELICATE ET SAINE"),
            "Poèmes des 4 saisons"=>array('name'=>"POEMES DES 4 SAISONS"),
            ];

    protected $_magasin = [
            'Primeur' => array("Poèmes des 4 saisons"),
            'Boucher' => array("Aux Viandes de Montmartre"),
            'Fromager' => array(""),
            'Poissonnier' => array("Poissonnerie du Dôme Damrémont"),
            'Caviste' => array(""),
            'Boulanger' => array(""),
            'Epicerie' => array('Délicate & Saine'),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

    protected $_googlesheets = [
            "Poèmes des 4 saisons"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1JEb8EMPke5KurIIEGrE38hxkL5cDFA-8sRKbUMiERQc',
                'street'=>'123 rue Caulaincourt Montmartre',
                'postcode'=>'75018',
                'phone'=>'',
                'code'=>'PDS18',
                'timetable'=>array("7:30-20:00","7:30-20:00","7:30-20:00","7:30-20:00","7:30-20:00","7:30-20:00","7:30-20:00",),
            ),
            "Poissonnerie du Dôme Damrémont"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1cNVtcpon90Po-u9wT5CO7qZbPdlm9z2f56nAOK1Uy88',
                'street'=>"61 rue Damrémont",
                'postcode'=>'75018',
                'phone'=>'',
                'code'=>'PDD18',
                'timetable'=>array("","08:30–13:00 / 16:00–19:30","08:30–13:00 / 16:00–19:30","08:30–13:00 / 16:00–19:30","08:30–13:00 / 16:00–19:30","08:30–13:00 / 16:00–19:30","09:00–13:00")
            ),
            "Délicate & Saine"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1tkt_EQSL2JhmgKgHuTkj6i4dQa_VmdFazVrhex45v1A',
                'street'=>'6 rue Ravignan',
                'postcode'=>'75018',
                'phone'=>'',
                'code'=>'DES18',
                'timetable'=>array("","12:00-21:30","12:00-21:30","12:00-21:30","12:00-21:30","11:00-21:30","11:00-20:00")
            ),
            "Aux Viandes de Montmartre"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1F13nk1uJCji_-cI3Ds7Hn0qivmDwosnTenx5HrdZfCQ',
                'street'=>'49 bis rue Custine',
                'postcode'=>'75018',
                'phone'=>'',
                'code'=>'AVM18',
                'timetable'=>array("8:00-13:00 / 15:30-20:00","8:00-13:00 / 15:30-20:00","8:00-13:00 / 15:30-20:00","8:00-13:00 / 15:30-20:00","8:00-13:00 / 15:30-20:00","8:00-20:00","8:00-13:00")
            )];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore18();
$shell->run();
    