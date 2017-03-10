<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore9 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 9e';
    protected $_rootcaturlkey = 'commercants-9e';
    protected $_codewebsite = 'apdc_9e';
    protected $_namewebsite = 'Au Pas De Courses 9e';
    protected $_namestoregroup = 'apdc_9e';
    protected $_codeboutique = 'paris9e';
    protected $_nameboutique = 'Paris 9e';
    protected $_city='Paris';
    protected $_zipcode=array('75009');
    protected $_country='FR';
    protected $_listmailchimp='News & Infos 9e';

    protected $_contacts = array(
            "Boucherie 9e" => [
                'firstname'=>'Boucherie 9e',
                'lastname'=>'Boucherie 9e',
                'email'=>'Boucherie 9e',
            ],
            "Boulangerie Lendemaine"=>[
                'firstname'=>'Boulangerie Lendemaine 9',
                'lastname'=>'Boulangerie Lendemaine 9',
                'email'=>'Boulangerie Lendemaine 9',
            ],
            "Cavavin"=>[
                'firstname'=>'Pascale',
                'lastname'=>'Prebet',
                'email'=>'paris9@cavavin.fr',
            ],
            "Au Verger Fleuri"=>[
                'firstname'=>'Au Verger Fleuri 9e',
                'lastname'=>'Au Verger Fleuri 9e',
                'email'=>'Au Verger Fleuri 9e',
            ],
            "Poissonnerie 9"=>[
                'firstname'=>'Poissonnerie 9',
                'lastname'=>'Poissonnerie 9',
                'email'=>'Poissonnerie 9',
            ],
            "Artisans du lait"=>[
                'firstname'=>'Artisans du lait 9',
                'lastname'=>'Artisans du lait 9',
                'email'=>'Artisans du lait9',
            ]);

    protected $_commercant = [
            "Boucherie 9e"=>array('name'=>'BOUCHERIE 9E'),
            "Boulangerie Lendemaine"=>array('name'=>'BOULANGERIE LENDEMAINE'),
            "Cavavin"=>array('name'=>"CAVAVIN"),
            "Au Verger Fleuri"=>array('name'=>'AU VERGER FLEURI'),
            "Poissonnerie 9"=>array('name'=>"POISSONNERIE 9"),
            "Artisans du lait"=>array('name'=>"ARTISANS DU LAIT"),
            ];

    protected $_magasin = [
            'Boucher' => "Boucherie 9e",
            'Boulanger' => "Boulangerie Lendemaine",
            'Caviste' => "Cavavin",
            'Primeur' => "Au Verger Fleuri",
            'Fromager' => "Artisans du lait",
            'Poissonnier' => "Poissonnerie 9",
            'Epicerie Fine' => '',
            'Traiteur' => '',
            ];

    protected $_googlesheets = [
            "Boucherie 9e"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            "Boulangerie Lendemaine"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1RPbPK3k-h2Hbgzluu_VB655dOhTTLRXCl1FjGm6-tXM',
            ),
            "Cavavin"=>array(
                'google_id'=>'1504362974',
                'google_key'=>'1GfqDkpfDSQCrIlp5EiAV9dx7s6yVkT2eeKVQQzA1uNg',
            ),
            "Au Verger Fleuri"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1GEQhLoAhR95Wl16pmNZinxxzQ4alEAAN6oQwY7bdGyU',
            ),
            "Poissonnerie 9"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            "Artisans du lait"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1KsfuFdJeZAePokE8MVcAPl1V6UMKHQjtkyAH4xVkxG8',
            ),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore9();
$shell->run();
    