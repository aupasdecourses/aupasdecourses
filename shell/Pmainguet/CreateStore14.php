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
    protected $_nameboutique = 'Paris 14e';
    protected $_city='Paris';
    protected $_zipcode=array('75014');
    protected $_country='FR';
    protected $_listmailchimp='News & Infos 14e';

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
                'firstname'=>'Achour',
                'lastname'=>'Primeur Gama',
                'email'=>'djidji751@hotmail.fr',
            ],
            "Poissonnerie L'Argonaute"=>[
                'firstname'=>'Eric',
                'lastname'=>'Argonaute',
                'email'=>'sarllargonaute1@yahoo.fr',
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
                'google_id'=>'1349743815',
                'google_key'=>'1ehzeE9x_jJsRkC9SyyBcMYq7dMxnacvazOaRsROI-Ps',
            ),
            'Le Pain Au Naturel'=>array(
                'google_id'=>'1504362974',
                'google_key'=>'1OYIpXIIfdEcbVEKSL-k_bROaGdDRK9QTbKZLZojLW60',
            ),
            "A l'ombre d'un bouchon"=>array(
                'google_id'=>'1504362974',
                'google_key'=>'1wAj2R4LY3T1-3VpfgppLLYhwZYMZn-ulHV59pcUGDFA',
            ),
            'Primeur de Gama'=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1E6d8fC5Hr319_tUYic0YmKI61wNwbwY4CnRdOS0rjAg',
            ),
            "Poissonnerie L'Argonaute"=>array(
                'google_id'=>'2131873323',
                'google_key'=>'1sPqKjkZnWVwCS-lQaVE7ESEyuSkne45tMhr7Wn9m3Tc',
            ),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore14();
$shell->run();
    