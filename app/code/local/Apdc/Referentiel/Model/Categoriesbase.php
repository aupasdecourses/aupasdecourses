<?php

class Apdc_Referentiel_Model_Categoriesbase extends Mage_Core_Model_Abstract
{
    public $limitsmall = 1;
    public $textcolor_l1 = '#ffffff';
    public $bgcolors_l1 = [
        'Boucher' => '#f3606f',
        'Boulanger' => '#f57320',
        'Caviste' => '#c62753',
        'Primeur' => '#3ab64b',
        'Fromager' => '#faae37',
        'Poissonnier' => '#5496d7',
        'Epicerie' => '#2f4da8',
        'Traiteur' => '#272b32',
        'Bio' => '#00595E',
        'Envies' => '#800040',
    ];
    public $maincats_l3 = ['Tous', 'Tout', 'Toute'];
    public $forbidden = ['#VALUE!', 'Tous les produits', 'Tous Les Produits','Au Verger du Hameau'];
        //$forbidden=['Cavavin','Les Bonnes Crèmes','Paris Terroirs','Paris Terroirs 5e','Les Papilles Gourmandes','Les Boucheries Francis','Paris Terroirs','La Mère-Mimosa','Paris Terroirs','Scübe'];
        //$forbidden=['#VALUE!','Detox\'','Noël','Menus','Evènements','Spécial été','Spécial Eté','Tous les produits','Tous Les Produits'];

    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/categoriesbase');
    }

}