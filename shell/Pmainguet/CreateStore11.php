<?php

require_once 'CreateStore.php';

class Pmainguet_CreateStore11 extends Pmainguet_CreateStore
{
    //PARAMETRES NOUVELLE BOUTIQUE A METTRE A JOUR
    protected $_rootcategory = 'Commercants 11e';
    protected $_rootcaturlkey = 'commercants-11e';
    protected $_codewebsite = 'apdc_11e';
    protected $_namewebsite = 'Au Pas De Courses 11e';
    protected $_namestoregroup = 'apdc_11e';
    protected $_codeboutique = 'paris11e';
    protected $_nameboutique = 'Paris 11e';
    protected $_city='Paris';
    protected $_zipcode=array('75011');
    protected $_country='FR';
    protected $_listmailchimp='Paris 11e';

    protected $_contacts = array(
            "Boucherie Milo" => [
                'firstname'=>'Monsieur',
                'lastname'=>'Milo',
                'email'=>'sacha.milo@hotmail.fr',
            ],
            "Boulangerie Dupain"=>[
                'firstname'=>'Contact commande',
                'lastname'=>'Boulangerie Dupain 11e',
                'email'=>'contact@aupasdecourses.com',
            ],
            "Paris Terroirs"=>[
                'firstname'=>'Dominique',
                'lastname'=>'Paris Terroirs',
                'email'=>'contact@aupasdecourses.com',
            ],
            "Primeur Clémentine"=>[
                'firstname'=>'Walid',
                'lastname'=>'Primeur Clémentine',
                'email'=>'walid7511@hotmail.fr',
            ],
            "Les poireaux de Marguerite"=>[
                'firstname'=>'Contact',
                'lastname'=>'Les poireaux de Marguerite',
                'email'=>'contact@aupasdecourses.com',
            ],
            "Fromagerie Goncourt"=>[
                'firstname'=>'Clément',
                'lastname'=>'Brossault',
                'email'=>'lafromageriegoncourt@gmail.com',
            ]);

    protected $_commercant = [
            "Boucherie Milo"=>array('name'=>'BOUCHERIE MILO'),
            "Boulangerie Dupain"=>array('name'=>'BOULANGERIE DUPAIN'),
            "Paris Terroirs"=>array('name'=>"PARIS TERROIRS"),
            "Primeur Clémentine"=>array('name'=>'PRIMEUR CLEMENTINE'),
            "Les poireaux de Marguerite"=>array('name'=>"LES POIREAUX DE MARGUERITE"),
            "Fromagerie Goncourt"=>array('name'=>'FROMAGERIE GONCOURT'),
            ];

    protected $_magasin = [
            'Boucher' => array("Boucherie Milo"),
            'Boulanger' => array("Boulangerie Dupain"),
            'Caviste' => array("Paris Terroirs"),
            'Primeur' => array("Primeur Clémentine","Les poireaux de Marguerite"),
            'Fromager' => array("Fromagerie Goncourt"),
            'Poissonnier' => array(""),
            'Epicerie' => array(''),
            'Traiteur' => array(''),
            'Bio' => array(''),
            ];

    protected $_googlesheets = [
            "Boucherie Milo"=>array(
                'google_id'=>'1349743815',
                'google_key'=>'1qXQUlR13YXsIPNOIpfvYSlj7R7GTxmVt3g_ANKcuvlc',
            ),
            "Boulangerie Dupain"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1HiGggRvRG7bATBbNfOQf8Iy3IY8ybyE5A_S-FYrZwA0',
            ),
            "Paris Terroirs"=>array(
                'google_id'=>'1504362974',
                'google_key'=>'1Hq4rqnknAlGzzKpeO2WLixAvAyr9eD29ro1QCjUA0Z4',
            ),
            "Primeur Clémentine"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'15MFR6lcVQQj151wwr4YqwY0jwGXM4c7G2yDUhMUgEoc',
            ),
            "Les poireaux de Marguerite"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1P3DYUMdTFQum-FQVyg7n6_r5OvyU1OFDjU7u2QVGu30',
            ),
            "Fromagerie Goncourt"=>array(
                'google_id'=>'2030347927',
                'google_key'=>'1oOaBtFHoFhE0oPX-7_7NiuWlZH6BL8EgqgIyVVCoewk',
            ),
            ];

}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore11();
$shell->run();
    