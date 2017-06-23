<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore12 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 12e';
    protected $_rootcaturlkey = 'commercants-12e';
    protected $_codewebsite = 'apdc_12e';
    protected $_namewebsite = 'Au Pas De Courses 12e';
    protected $_namestoregroup = 'apdc_12e';
    protected $_codeboutique = 'paris12e';
    protected $_nameboutique = 'Paris 12e';
    protected $_city='Paris';
    protected $_zipcode=array('75012');
    protected $_country='FR';
    protected $_listmailchimp='Paris 12e';

    protected $_contacts = array(
            "Boucherie Francis" => [
                'firstname'=>'Francis',
                'lastname'=>'Berger',
                'email'=>'fberger@lesboucheriesfrancis.com',
                'phone'=>'06 99 40 38 61',
            ],
            "La Poissonnerie d'Aligre"=>[
                'firstname'=>'Patrick',
                'lastname'=>'Dubuisson',
                'email'=>'lapoissonneriedaligre@achangermail.com',
            ],
            "Le Raisin Bleu"=>[
                'firstname'=>'Contact',
                'lastname'=>'Le Raisin Bleu',
                'email'=>'raisinbleu@achanger.com',
            ],
            "Le Verger d'Aligre"=>[
                'firstname'=>'Salim',
                'lastname'=>'Chaib',
                'email'=>'dida230982@gmail.com',
            ],
            "Fromagerie Riondet"=>[
                'firstname'=>'Justine & Kevin',
                'lastname'=>'Riondet',
                'email'=>'fromagerie.riondet@gmail.com',
            ],
            "Boulangerie 12e"=>[
                'firstname'=>'Contact',
                'lastname'=>'Boulangerie 12e',
                'email'=>'boulangerie12@achanger.com',
            ]);

    protected $_commercant = [
            "Boucherie Francis"=>array('name'=>'BOUCHERIE FRANCIS'),
            "La Poissonnerie d'Aligre"=>array('name'=>"LA POISSONNERIE D'ALIGRE"),
            "Le Raisin Bleu"=>array('name'=>"LE RAISIN BLEU"),
            "Le Verger d'Aligre"=>array('name'=>"LE VERGER D'ALIGRE"),
            "Boulangerie 12e"=>array('name'=>"BOULANGERIE 12E"),
            "Fromagerie Riondet"=>array('name'=>"FROMAGERIE RIONDET"),
            ];

    protected $_magasin = [
            'Primeur' => array("Le Verger d'Aligre"),
            'Boucher' => array("Boucherie Francis"),
            'Fromager' => array("Fromagerie Riondet"),
            'Poissonnier' => array("La Poissonnerie d'Aligre"),
            'Caviste' => array("Le Raisin Bleu"),
            'Boulanger' => array("Boulangerie 12e"),
            'Epicerie' => array(''),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

    protected $_googlesheets = [
            "Boucherie Francis"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1QohUKEnYRoB_YQ5_mngHJgWyd_6kJyHdtUgvBTFpcHA',
                'street'=>'7 Rue de Chaligny',
                'postcode'=>'75012',
                'phone'=>'',
                'code'=>'BFR12',
                'timetable'=>array("8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","")
            ),
            "La Poissonnerie d'Aligre"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1l1TE7d0z8bFaJDgq6zykl2FlGFJlow9YXY1Dc83Pka4',
                'street'=>"7 Place d'Aligre",
                'postcode'=>'75012',
                'phone'=>'',
                'code'=>'LPA12',
                'timetable'=>array("08:00-20:00","08:00-20:00","08:00-20:00","08:00-20:00","08:00-20:00","08:00-20:00","08:00-20:00")
            ),
            "Le Raisin Bleu"=>array(
                'google_id'=>'A changer',
                'google_key'=>'A changer',
                'street'=>'A changer',
                'postcode'=>'75012',
                'phone'=>'',
                'code'=>'A changer',
                'timetable'=>array("","","","","","","")
            ),
            "Fromagerie Riondet"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1wk-ItjS4F07g7qyAerz5NeF4-H3bgVVL9OfhS2e-PT4',
                'street'=>'25 rue Erard',
                'postcode'=>'75012',
                'phone'=>'FRD12',
                'code'=>'A changer',
                'timetable'=>array("","10:00-14:00 // 16:00-20:00","10:00-14:00 // 16:00-20:00","10:00-14:00 // 16:00-20:00","10:00-14:00 // 16:00-20:00","10:00-14:00 // 16:00-20:00","")
            ),
            "Le Verger d'Aligre"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1T6nm1UHVA1DVBx2OpEep8p9f5c3rlmPoIRZ12W1KbAk',
                'street'=>'7 Place d\'Aligre',
                'postcode'=>'75012',
                'phone'=>'',
                'code'=>'VDA12',
                'timetable'=>array("","9:00-13:00 // 15:30-19:30","9:00-13:00 // 15:30-19:30","9:00-13:00 // 15:30-19:30","9:00-13:00 // 15:30-19:30","9:00-13:00 // 15:30-19:30","9:00-13:00")
            )];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore12();
$shell->run();
    