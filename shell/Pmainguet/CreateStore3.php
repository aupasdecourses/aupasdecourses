<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore3 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 3e';
    protected $_rootcaturlkey = 'commercants-3e';
    protected $_codewebsite = 'apdc_3e';
    protected $_namewebsite = 'Au Pas De Courses 3e';
    protected $_namestoregroup = 'apdc_3e';
    protected $_codeboutique = 'paris3e';
    protected $_nameboutique = 'Paris 3e';
    protected $_city='Paris';
    protected $_zipcode=array('75003');
    protected $_country='FR';
    protected $_listmailchimp='Paris 3e';

    protected $_contacts = array(
            "Boucherie des Gravilliers" => [
                'firstname'=>'Manu',
                'lastname'=>'Boucherie Gravilliers',
                'email'=>'boucherie.manu@hotmail.com',
                'phone'=>'01 42 77 55 24',
            ],
            "La Cabane du Pêcheur"=>[
                'firstname'=>'Contact',
                'lastname'=>'La Cabane du Pêcheur',
                'email'=>'cabanepeche@achangermail.com',
            ],
            "Terres de Café Blancs Manteaux"=>[
                'firstname'=>'Contact',
                'lastname'=>'Terres de Café Blancs Manteaux',
                'email'=>'terresdecafe@achanger.com',
            ],
            "Chez Wagner Primeur Bio"=>[
                'firstname'=>'Bouchra',
                'lastname'=>'Baltim Wagner',
                'email'=>'aucoinbio.bwagner@gmail.com',
            ],
            "La Petite Ferme d'Inès"=>[
                'firstname'=>'Contact',
                'lastname'=>'La Petite Ferme d\'Inès',
                'email'=>'slimanianesic@gmail.com',
            ]);

    protected $_commercant = [
            "Boucherie des Gravilliers"=>array('name'=>'BOUCHERIE DES GRAVILLIERS'),
            "La Cabane du Pêcheur"=>array('name'=>"LA CABANE DU PECHEUR"),
            "Terres de Café Blancs Manteaux"=>array('name'=>"TERRES DE CAFE"),
            "Chez Wagner Primeur Bio"=>array('name'=>"CHEZ WAGNER PRIMEUR BIO"),
            "La Petite Ferme d'Inès"=>array('name'=>"LA PETITE FERME D'INES"),
            ];

    protected $_magasin = [
            'Primeur' => array("Chez Wagner Primeur Bio"),
            'Boucher' => array("Boucherie des Gravilliers"),
            'Fromager' => array("La Petite Ferme d'Inès"),
            'Poissonnier' => array("La Cabane du Pêcheur"),
            'Caviste' => array(""),
            'Boulanger' => array(""),
            'Epicerie' => array('Terres de Café Blancs Manteaux'),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

    protected $_googlesheets = [
            "Boucherie des Gravilliers"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1nQUDi1YxHoJfeZt6h4SGl8dXQ6-r3tyrQI9CHEsL5W8',
                'street'=>'28 rue des Gravilliers',
                'postcode'=>'75003',
                'phone'=>'',
                'code'=>'BDG03',
                'timetable'=>array("9:00-13:30 / 16:00-20:30","9:00-13:30 / 16:00-20:30","9:00-13:30 / 16:00-20:30","9:00-13:30 / 16:00-20:30","9:00-13:30 / 16:00-20:30","9:00-14:00 / 16:00-20:00","10:30-14:00"),
            ),
            "La Cabane du Pêcheur"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1pDUIzhYj-iXvS3K7xyfrTeer9M3clX0q_Ijk1Bc7R_w',
                'street'=>"-",
                'postcode'=>'75003',
                'phone'=>'',
                'code'=>'LCP03',
                'timetable'=>array("","","","","","","")
            ),
            "La Petite Ferme d'Inès"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1er7Ma_FSgmS92N6UA8S71W-OzWhMyyj4H6Ixhhjui9M',
                'street'=>'39 rue de Bretagne',
                'postcode'=>'75003',
                'phone'=>'',
                'code'=>'LPF03',
                'timetable'=>array("","9:00-20:00","9:00-20:00","9:00-20:00","9:00-20:00","9:00-20:00","")
            ),
            "Chez Wagner Primeur Bio"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'15fmm05CE4kC7oYjpYuXT8K45-eFPasPJtrhONikiM3Y',
                'street'=>'39 rue de Bretagne',
                'postcode'=>'75003',
                'phone'=>'01 42 78 43 15',
                'code'=>'CWP03',
                'timetable'=>array("","-","-","-","-","-","-")
            ),
            "Terres de Café Blancs Manteaux"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1mAYOjbuHt5DF8D01Rz0GGiaQ4Do2JyQ1njD8DGFJeXs',
                'street'=>'40 Rue des Blancs Manteaux',
                'postcode'=>'75004',
                'phone'=>'',
                'code'=>'TDC04',
                'timetable'=>array("","9:30-19:00","9:30-19:00","9:30-19:00","9:30-19:00","9:30-19:00","13:00-18:00")
            )];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore3();
$shell->run();
    