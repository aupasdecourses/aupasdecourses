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
    protected $_zipcode=array('75009','75018','75002');
    protected $_country='FR';
    protected $_listmailchimp='Paris 9e';

    protected $_contacts = array(
            "Boucherie 9e" => [
                'firstname'=>'Contact commande',
                'lastname'=>'Boucherie 9e',
                'email'=>'contact@aupasdecourses.com',
            ],
            "Boulangerie Lendemaine"=>[
                'firstname'=>'Contact commande',
                'lastname'=>'Boulangerie Lendemaine 9',
                'email'=>'contact@aupasdecourses.com',
            ],
            "Cavavin"=>[
                'firstname'=>'Pascale',
                'lastname'=>'Prebet',
                'email'=>'paris9@cavavin.fr',
            ],
            "Au Verger Fleuri"=>[
                'firstname'=>'Abdel',
                'lastname'=>'Au Verger Fleuri 9e',
                'email'=>'abdelmajidkarouia@yahoo.fr',
            ],
            "Au Bon Port Montmartre"=>[
                'firstname'=>'Hakim',
                'lastname'=>'Au Bon Port Montmartre',
                'email'=>'contact@aupasdecourses.com',
            ],
            "Les Bonnes Crèmes"=>[
                'firstname'=>'Contact',
                'lastname'=>'Les Bonnes Crèmes 9e',
                'email'=>'contact@aupasdecourses.com',
            ],
            "Artisans du lait"=>[
                'firstname'=>'Ruben',
                'lastname'=>'Artisans du lait 9',
                'email'=>'ruben@artisandulait.fr',
            ]);

    protected $_commercant = [
            "Boucherie 9e"=>array('name'=>'BOUCHERIE 9E'),
            "Boulangerie Lendemaine"=>array('name'=>'BOULANGERIE LENDEMAINE'),
            "Cavavin"=>array('name'=>"CAVAVIN"),
            "Au Verger Fleuri"=>array('name'=>'AU VERGER FLEURI'),
            "Au Bon Port Montmartre"=>array('name'=>"AU BON PORT JEANNE D'ARC"),
            "Artisans du lait"=>array('name'=>"ARTISANS DU LAIT"),
            "Les Bonnes Crèmes"=>array('name'=>"LES BONNES CREMES"),
            ];

    protected $_magasin = [
            'Boucher' => "Boucherie 9e",
            'Boulanger' => "Boulangerie Lendemaine",
            'Caviste' => "Cavavin",
            'Primeur' => "Au Verger Fleuri",
            'Fromager' => "Artisans du lait",
            'Poissonnier' => "Au Bon Port Montmartre",
            'Epicerie' => 'Les Bonnes Crèmes',
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
                'google_key'=>'1003mGaSpIRfH1JykpOtUjsnZPeKSsWvwZ68BhrHn4nM',
            ),
            "Au Bon Port Montmartre"=>array(
                'google_id'=>'2131873323',
                'google_key'=>'1-nVGZYkYPFPIlDYZ7geUarfSV1jL5Qeq5hkRA58ngwI',
            ),
            "Artisans du lait"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1KsfuFdJeZAePokE8MVcAPl1V6UMKHQjtkyAH4xVkxG8',
            ),
            "Les Bonnes Crèmes"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore9();
$shell->run();
    