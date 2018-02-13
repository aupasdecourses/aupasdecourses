<?php

class Apdc_Catalog_Helper_Product_Labels extends Mage_Core_Helper_Abstract
{
	public function getImageLabels(){
		return array(
			"Produit Bio" => $this->getLabelBio(),
			"Produit De Saison" => $this->getLabelSaison(),
			"Nouveau Produit" => $this->getLabelNew(),
		);
	}

	public function getAuthorizeLabels(){
		return [
            'AOC' => 'images/labels/AOC.png',
            'DOC' => 'images/labels/AOC.png',
            'DOP' => 'images/labels/AOC.png',
            'AOP' => 'images/labels/AOC.png',
            'IGP' => 'images/labels/AOC.png',
            'Demeter' => 'images/labels/demeter.png',
            'Label Rouge' => 'images/labels/labelrouge.png',
            'Bleu blanc coeur' => 'images/labels/bleublanccoeur.png',
            'Bleu Blanc Coeur' => 'images/labels/bleublanccoeur.png',
            'Commerce équitable' => 'images/labels/fairtrade.png',
            'Commerce Equitable' => 'images/labels/fairtrade.png',
            'Casher' => 'images/labels/casher.png',
            'Kasher' => 'images/labels/casher.png',
            'Halal' => 'images/labels/halal.png',
        ];
	}

	public function getAuthorizeOrigine(){
        return [
                "Afrique du Sud" => "",
                "Algérie" => "",
                "Allemagne" => "images/labels/countries/europe.png",
                "Angleterre" => "images/labels/countries/europe.png",
                "Antilles" => "",
                "Argentine" => "",
                "Atlantique " => "",
                "Atlantique Centre-Est" => "",
                "Atlantique Nord" => "",
                "Atlantique Nord-Est" => "",
                "Australie" => "",
                "Autriche" => "images/labels/countries/europe.png",
                "Belgique" => "images/labels/countries/europe.png",
                "Bénin" => "",
                "Biélorussie" => "",
                "Brésil" => "",
                "Bulgarie" => "images/labels/countries/europe.png",
                "Cameroun" => "",
                "Canada" => "",
                "Chili" => "",
                "Chine" => "",
                "Colombie" => "",
                "Costa Rica" => "",
                "Côte d'Ivoire" => "",
                "Crète" => "images/labels/countries/europe.png",
                "Cuba" => "",
                "Danemark" => "images/labels/countries/europe.png",
                "Ecosse" => "images/labels/countries/europe.png",
                "Egypte" => "",
                "Equateur" => "",
                "Espagne" => "images/labels/countries/europe.png",
                "Ethiopie" => "",
                "Europe" => "images/labels/countries/europe.png",
                "France" => "images/labels/countries/france.png",
                "Ghana" => "",
                "Grande Bretagne" => "images/labels/countries/europe.png",
                "Grèce" => "images/labels/countries/europe.png",
                "Hollande" => "images/labels/countries/europe.png",
                "Honduras" => "",
                "Ile Maurice" => "",
                "Iran" => "",
                "Irlande" => "images/labels/countries/europe.png",
                "Islande " => "images/labels/countries/europe.png",
                "Israël" => "",
                "Italie " => "images/labels/countries/europe.png",
                "Japon" => "",
                "Jordanie" => "",
                "Kenya" => "",
                "La Réunion" => "images/labels/countries/france.png",
                "Lituanie" => "images/labels/countries/europe.png",
                "Madagascar" => "",
                "Manche" => "",
                "Maroc" => "",
                "Martinique" => "images/labels/countries/france.png",
                "Méditerranée" => "",
                "Mer Baltique" => "",
                "Mexique" => "",
                "Norvège" => "images/labels/countries/europe.png",
                "Nouvelle-Zélande" => "",
                "Océan Indien" => "",
                "Ouganda" => "",
                "Pacifique Nord" => "",
                "Pays-Bas" => "images/labels/countries/europe.png",
                "Pérou" => "",
                "Pologne" => "images/labels/countries/europe.png",
                "Portugal" => "images/labels/countries/europe.png",
                "République Dominicaine" => "",
                "République Tchèque" => "images/labels/countries/europe.png",
                "Réunion" => "images/labels/countries/france.png",
                "Russie" => "",
                "Sardaigne" => "images/labels/countries/europe.png",
                "Sénégal" => "",
                "Sicile" => "images/labels/countries/europe.png",
                "Suisse" => "images/labels/countries/europe.png",
                "Togo" => "",
                "Tunisie" => "",
                "Turquie" => "",
                "Uruguay" => "",
                "Venezuela" => "",
                "Vietnam" => "",
        ];
     }

     public function getLabelNew(){
     	return 'images/labels/new.png';
     }

     public function getLabelBio(){
     	return 'images/labels/bio.png';
     }

     public function getLabelSaison(){
     	return 'images/labels/saison.png';
     }

     public function getLabelRupture(){
     	return 'images/labels/rutpure.png';
     }
}