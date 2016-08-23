<?php

  class Pmainguet_Commercants_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
  {
  
	////* VARIABLES *////

  	private $label=['commercant_id','category','attribute_label','adresse','logo','photo','description','horaires','badge','jour_livraison'];
	const MODEL='commercants_model/fiche1';

  	
	////* FONCTIONS GENERALES *////

  	/*Fonctions générales pour alléger le code des actions*/

	public function gettable(){
	    $fiche = Mage::getModel(self::MODEL);
	    return $fiche;
  	}

  	//En duplicata d'une fonction du block Fiche => voir si possible d'utiliser Helper pour la partager entre les deux
  	public function strtoupperFr($string) {
		$string = strtoupper($string);
		$string = str_replace(
		array('é', 'è', 'ê', 'ë', 'à', 'â', 'î', 'ï', 'ô', 'ù', 'û'),
		array('E', 'E', 'E', 'E', 'A', 'A', 'I', 'I', 'O', 'U', 'U'),
		$string
		);
		return $string;
	}

  	////*ACTIONS *////

  	/*Action utilisée avec l'URL admincommercants/adminhtml_index/index */

    public function indexAction()
    {
      $this->loadLayout();
	    $this->renderLayout();
    }


    /*Action utilisée avec l'URL admincommercants/adminhtml_index/edit (formulaire) */

	public function editAction()
	 {
	    //on recuperes les données envoyées en POST
	    foreach ($this->label as $value){
	    	$$value = ''.$this->getRequest()->getPost($value);
	    }
	    //A faire: Ajouter de la vérification de formulaire (pour éviter erreur)

	      $fiche=$this->gettable();
		  $fiche->load($commercant_id);
		  foreach($this->label as $value){
		  	switch($value){
		  		case 'commercant_id':
		  			break;
		  		default:
		  			$fiche->setData($value,$$value);
	   	  }}
		  $fiche->save();
		
	   //Redirection
	   $this->_redirect('admincommercants/adminhtml_index');
	}
	
	/*Action utilisée avec l'URL admincommercants/adminhtml_index/save (formulaire) */

	public function newAction(){

		foreach ($this->label as $value){
	    	$$value = ''.$this->getRequest()->getPost($value);
	    }

		$fiche=$this->gettable();
		  foreach($this->label as $value){
		  	switch($value){
		  		case 'commercant_id':
		  			break;
		  		default:
		  			$fiche->setData($value,$$value);
	   	  }}
		$fiche->save();

		$this->_redirect('admincommercants/adminhtml_index');

	}

	public function deleteAction() {
		$commercant_id = ''.$this->getRequest()->getPost('commercant_id');
	    $fiche=$this->gettable();
	    $fiche->load($commercant_id);
	    $fiche->delete();
	    $this->_redirect('admincommercants/adminhtml_index');
	} 

}

?>