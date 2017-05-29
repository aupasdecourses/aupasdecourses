<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore5 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 5e';
    protected $_rootcaturlkey = 'commercants-5e';
    protected $_codewebsite = 'apdc_5e';
    protected $_namewebsite = 'Au Pas De Courses 5e';
    protected $_namestoregroup = 'apdc_5e';
    protected $_codeboutique = 'paris5e';
    protected $_nameboutique = 'Paris 5e';
    protected $_city='Paris';
    protected $_zipcode=array('75005');
    protected $_country='FR';
    protected $_listmailchimp='Paris 5e';

    protected $_contacts = array(
            "Boucherie Saint Médard" => [
                'firstname'=>'Pascal',
                'lastname'=>'Gosnet',
                'email'=>'pascalgosnet94@gmail.com',
            ],
            "Poissonnerie La Boulonnaise"=>[
                'firstname'=>'Yves',
                'lastname'=>'Live Me Gei',
                'email'=>'landry171@gmail.com',
            ],
            "Paris Terroirs 5e"=>[
                'firstname'=>'Dominique',
                'lastname'=>'Paris Terroirs',
                'email'=>'dtissier@paristerroirs.com',
            ],
            "Au Jardin de Mouffetard"=>[
                'firstname'=>'Ben Rhouma',
                'lastname'=>'Abdelkader',
                'email'=>'mouffetardjardin@achanger.com',
            ],
            "Boulangerie 5e"=>[
                'firstname'=>'Contact',
                'lastname'=>'Boulangerie 5e',
                'email'=>'bouglangerie5eachanger.com',
            ]);

    protected $_commercant = [
            "Boucherie Saint Médard"=>array('name'=>'BOUCHERIE SAINT MEDARD'),
            "Poissonnerie La Boulonnaise"=>array('name'=>'POISSONNERIE LA BOULONNAISE'),
            "Paris Terroirs 5e"=>array('name'=>"PARIS TERROIRS"),
            "Au Jardin de Mouffetard"=>array('name'=>'AU JARDIN DE MOUFFETARD'),
            "Boulangerie 5e"=>array('name'=>"BOULANGERIE 5E"),
            ];

    protected $_magasin = [
            'Primeur' => array("Au Jardin de Mouffetard"),
            'Boucher' => array("Boucherie Saint Médard"),
            'Fromager' => array(""),
            'Poissonnier' => array("Poissonnerie La Boulonnaise"),
            'Caviste' => array("Paris Terroirs 5e"),
            'Boulanger' => array("Boulangerie 5e"),
            'Epicerie' => array(''),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

    protected $_googlesheets = [
            "Boucherie Saint Médard"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1OKQTXMHK28fpM8GRBq7S76KHl5Eb5UZU1y6e_fAfHlQ',
                'street'=>'119 Rue Mouffetard',
                'postcode'=>'75005',
                'phone'=>'01 45 35 14 72',
                'code'=>'BSM05',
                'timetable'=>array("8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","8:00-20:00","")
            ),
            "Poissonnerie La Boulonnaise"=>array(
                'google_id'=>'2131873323',
                'google_key'=>'1reeDmumCE-NCjBosFJ7Nbrhbd0xaLvoBKMoTHXs9anM',
                'street'=>'47 Boulevard Saint-Germain',
                'postcode'=>'75005',
                'phone'=>'01 43 54 03 01',
                'code'=>'PLB05',
                'timetable'=>array("","10:00-13:00 // 16:00-20:00","10:00-13:00 // 16:00-20:00","10:00-13:00 // 16:00-20:00","10:00-13:00 // 16:00-20:00","10:00-13:00 // 16:00-20:00","")
            ),
            "Paris Terroirs 5e"=>array(
                'google_id'=>'1504362974',
                'google_key'=>'1Q-vv6zIm0AqSXrXdZspd0eeDBc4ZgB_SiDQ7wES85Q4',
                'street'=>'57 rue Monge',
                'postcode'=>'75005',
                'phone'=>'01 43 57 92 97',
                'code'=>'PAT05',
                'timetable'=>array("","9:00-13:00 // 16:00-20:30","9:00-13:00 // 16:00-20:30","9:00-13:00 // 16:00-20:30","9:00-13:00 // 16:00-20:30","9:00-13:00 // 16:00-20:30","")
            ),
            "Au Jardin de Mouffetard"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1ZfWrAIz_z9LHHTPu0VXqe3g89ydMLMvM-tesywBnGOs',
                'street'=>'130 rue Mouffetard',
                'postcode'=>'75005',
                'phone'=>'01 43 57 92 97',
                'code'=>'LJM05',
                'timetable'=>array("","9:00-19:30","9:00-19:30","9:00-19:30","9:00-19:30","9:00-19:30","")
            )];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore5();
$shell->run();
    