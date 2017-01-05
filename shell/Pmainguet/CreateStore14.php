<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore14 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 14e';
    protected $_rootcaturlkey = 'commercants-14e';
    protected $_codewebsite = 'apdc_14e';
    protected $_namewebsite = 'Au Pas De Courses 14e';
    protected $_namestoregroup = 'apdc_14e';
    protected $_codeboutique = 'paris14e';
    protected $_nameboutique = '14e';
    protected $_city='Paris';
    protected $_zipcode=array('75014');
    protected $_country='FR';

    protected $_contacts = [
            'Boucherie Pernety'=>[
                'firstname'=>'Olivier',
                'lastname'=>'Bellossat',
                'email'=>'olivierbellossat@yahoo.fr',
            ],
            'Le Pain Au Naturel'=>[
                'firstname'=>'Florian',
                'lastname'=>'Perraudin',
                'email'=>'fperraudin@painmoisan.fr',
            ],
            "A l'ombre d'un bouchon"=>[
                'firstname'=>'Daley',
                'lastname'=>'Brennan',
                'email'=>'contact@alombredunbouchon.com',
            ],
            "Primeur de Gama"=>[
                'firstname'=>'xxx',
                'lastname'=>'Achour',
                'email'=>'xxxachour@test.fr',
            ],
            "Poissonnerie L'Argonaute"=>[
                'firstname'=>'poissonnerie',
                'lastname'=>'argonaute',
                'email'=>'test@argonautue.com',
            ]];

    protected $_commercant = [
            'Boucherie Pernety'=>array('name'=>'BOUCHERIE PERNETY'),
            'Le Pain Au Naturel'=>array('name'=>'BOULANGERIE MOISAN'),
            "A l'ombre d'un bouchon"=>array('name'=>"A L'OMBRE D'UN BOUCHON"),
            'Primeur de Gama'=>array('name'=>'PRIMEUR DE GAMA'),
            "Poissonnerie L'Argonaute"=>array('name'=>"POISSONNERIE L'ARGONAUTE"),
            ];

    protected $_magasin = [
            'Boucher' => 'Boucherie Pernety',
            'Boulanger' => 'Le Pain Au Naturel',
            'Caviste' => "A l'ombre d'un bouchon",
            'Primeur' => 'Primeur de Gama',
            'Fromager' => '',
            'Poissonnier' => "Poissonnerie L'Argonaute",
            'Epicerie Fine' => '',
            'Traiteur' => '',
            ];

    protected $_googlesheets = [
            'Boucherie Pernety'=>array(
                'google_id'=>'test',
                'google_key'=>'test',
            ),
            'Le Pain Au Naturel'=>array(
                'google_id'=>'e',
                'google_key'=>'e',
            ),
            "A l'ombre d'un bouchon"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            'Primeur de Gama'=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            "Poissonnerie L'Argonaute"=>array(
                'google_id'=>'',
                'google_key'=>'',
            ),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore14();
$shell->run();
    