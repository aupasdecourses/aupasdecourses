<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore7 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 7e';
    protected $_rootcaturlkey = 'commercants-7e';
    protected $_codewebsite = 'apdc_7e';
    protected $_namewebsite = 'Au Pas De Courses 7e';
    protected $_namestoregroup = 'apdc_7e';
    protected $_codeboutique = 'paris7e';
    protected $_nameboutique = 'Paris 7e';
    protected $_city='Paris';
    protected $_zipcode=array('75007');
    protected $_country='FR';
    protected $_listmailchimp='News & Infos 7e';

    protected $_contacts = array(
            "Les viandes du Champ de Mars" => [
                'firstname'=>'Mr',
                'lastname'=>'Boadec',
                'email'=>'kenavo.boedec@wanadoo.fr',
            ],
            "Boulangerie 7e"=>[
                'firstname'=>'Boulangerie 7e',
                'lastname'=>'Boulangerie 7e',
                'email'=>'Boulangerie 7e',
            ],
            "Les Petits Domaines"=>[
                'firstname'=>'petits',
                'lastname'=>'domaines',
                'email'=>'xxx@xxx.com',
            ],
            "Harry Cover"=>[
                'firstname'=>'Julien',
                'lastname'=>'Gérard',
                'email'=>'xxx@xxx.com',
            ],
            "Poissonnerie 7e"=>[
                'firstname'=>'Poissonnerie 7e',
                'lastname'=>'Poissonnerie 7e',
                'email'=>'Poissonnerie 7e',
            ],
            "Fromagerie 7e"=>[
                'firstname'=>'Fromagerie 7e',
                'lastname'=>'Fromagerie 7e',
                'email'=>'Fromagerie 7e',
            ]);

    protected $_commercant = [
            "Les viandes du Champ de Mars"=>array('name'=>'LES VIANDES DU CHAMP DE MARS'),
            "Boulangerie 7e"=>array('name'=>'BOULANGERIE 7E'),
            "Les Petits Domaines"=>array('name'=>"LES PETITS DOMAINES"),
            "Harry Cover"=>array('name'=>'HARRY COVER'),
            "Poissonnerie 7e"=>array('name'=>"POISSONNERIE 7E"),
            "Fromagerie 7e"=>array('name'=>"FROMAGERIE 7E"),
            ];

    protected $_magasin = [
            'Boucher' => "Les viandes du Champ de Mars",
            'Boulanger' => "Boulangerie 7e",
            'Caviste' => "Les Petits Domaines",
            'Primeur' => "Harry Cover",
            'Fromager' => "Fromagerie 7e",
            'Poissonnier' => "Poissonnerie 7e",
            'Epicerie' => '',
            'Traiteur' => '',
            ];

    protected $_googlesheets = [
            "Les viandes du Champ de Mars"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1jzvZHpG7MLhG8kD9o1KwUHmfopiY5a0ygRHJkkX0L0s',
            ),
            "Boulangerie 7e"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            "Les Petits Domaines"=>array(
                'google_id'=>'1504362974',
                'google_key'=>'1eNit7RsDmKYxV7RadFUvEXBZ5FnOmrTwKVxPaCIP2Fo',
            ),
            "Harry Cover"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'14iSjUaB74q0EtYdsO6gvBbLAMp_ByC0jZAVEC54Br0Y',
            ),
            "Poissonnerie 7e"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            "Fromagerie 7e"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore7();
$shell->run();
    