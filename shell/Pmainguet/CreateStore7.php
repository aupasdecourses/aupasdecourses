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
    protected $_city = 'Paris';
    protected $_zipcode = array('75007');
    protected $_country = 'FR';
    protected $_listmailchimp = 'News & Infos 7e';

    protected $_contacts = array(
            'Les Viandes du Champ de Mars' => [
                'firstname' => 'Mr',
                'lastname' => 'Boedec',
                'email' => 'kenavo.boedec@wanadoo.fr',
            ],
            'Le Champ des Délices' => [
                'firstname' => 'Sandra',
                'lastname' => 'Le Champ des Délices',
                'email' => 'lcd7apdc@gmail.com',
            ],
            'Les Petits Domaines' => [
                'firstname' => 'Isabelle',
                'lastname' => 'Petits Domaines',
                'email' => 'isabelle@lespetitsdomaines.com',
            ],
            'Harry Cover' => [
                'firstname' => 'Julien',
                'lastname' => 'Ioli',
                'email' => 'xxx@xxx.com',
            ],
            'Poissonnerie 7e' => [
                'firstname' => 'Poissonnerie 7e',
                'lastname' => 'Poissonnerie 7e',
                'email' => 'Poissonnerie 7e',
            ],
            'Fromagerie Cantin' => [
                'firstname' => 'Antoine',
                'lastname' => 'Dias',
                'email' => 'contact@cantin.fr',
            ], );

    protected $_commercant = [
            'Les Viandes du Champ de Mars' => array('name' => 'LES VIANDES DU CHAMP DE MARS'),
            'Le Champ des Délices' => array('name' => 'LE CHAMP DES DELICES'),
            'Les Petits Domaines' => array('name' => 'LES PETITS DOMAINES'),
            'Harry Cover' => array('name' => 'HARRY COVER'),
            'Poissonnerie 7e' => array('name' => 'POISSONNERIE 7E'),
            'Fromagerie Cantin' => array('name' => 'FROMAGERIE CANTIN'),
            ];

    protected $_magasin = [
            'Boucher' => 'Les Viandes du Champ de Mars',
            'Boulanger' => 'Le Champ des Délices',
            'Caviste' => 'Les Petits Domaines',
            'Primeur' => 'Harry Cover',
            'Fromager' => 'Fromagerie Cantin',
            'Poissonnier' => 'Poissonnerie 7e',
            'Epicerie' => '',
            'Traiteur' => '',
            ];

    protected $_googlesheets = [
            'Les Viandes du Champ de Mars' => array(
                'google_id' => '2030347927',
                'google_key' => '1jzvZHpG7MLhG8kD9o1KwUHmfopiY5a0ygRHJkkX0L0s',
            ),
            'Le Champ des Délices' => array(
                'google_id' => '2030347927',
                'google_key' => '1uCsvSg8x9a7DhF-9VTMw35lJ4oEryK-e2Kw6yc7x2jo',
            ),
            'Les Petits Domaines' => array(
                'google_id' => '1504362974',
                'google_key' => '1eNit7RsDmKYxV7RadFUvEXBZ5FnOmrTwKVxPaCIP2Fo',
            ),
            'Harry Cover' => array(
                'google_id' => '2030347927',
                'google_key' => '14iSjUaB74q0EtYdsO6gvBbLAMp_ByC0jZAVEC54Br0Y',
            ),
            'Poissonnerie 7e' => array(
                'google_id' => '',
                'google_key' => '',
            ),
            'Fromagerie Cantin' => array(
                'google_id' => '2030347927',
                'google_key' => '1uCsvSg8x9a7DhF-9VTMw35lJ4oEryK-e2Kw6yc7x2jo',
            ),
            ];
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore7();
$shell->run();
