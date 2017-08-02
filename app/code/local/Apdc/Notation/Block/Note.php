<?php
/**
 * @author APDC Pierre Mainguet
 * @copyright Copyright (c) 2017 APDC
 * @package Apdc_Notation
 */
class Apdc_Notation_Block_Note extends Mage_Core_Block_Template        
{
    private $_note;
    const MAX_NOTE=5; 

    public function __construct()
    {
    	$note=(int)$this->getRequest()->getParam('note');
    	$this->setNote($note);
    }

    public function setNote($n)
    {
    	return $this->_note=$n;
    }

    public function getNote()
    {
    	return $this->_note;
    }

    public function getStars()
    {
    	$note=$this->getNote();
    	
    	//HTML elements
    	$base='<i class="fa fa-2x ';
    	$tag_normal=' fa-star ';
    	$tag_empty=' fa-star-o ';

    	$html="";

    	for($i=1; $i<=self::MAX_NOTE;$i++){
    		$html.=$base;
    		if($i<=$note){
    			$html.=$tag_normal;
    		}else{
    			$html.=$tag_empty;
    		}
    		$html.='"></i>';
    	}

    	return $html;

    }

    public function getAdditional(){
    	
    	$note=$this->getNote();
    	$html='<div class="additional-stars">';

        $_helper=Mage::helper('apdccustomer');
        $url=$_helper->getCustomerWebsite().'customer/account/';

    	if($note<=3){
    		$html.='<p style="font-size:16px;">Notre objectif est de vous apporter le meilleur service, </br>alors si vous souhaitez nous aider à nous améliorer, vous pouvez répondre <a href="https://docs.google.com/forms/d/e/1FAIpQLSf47RPA2wzgbE1siha4yphX_Ya5HDnXd_1XF19VuM1Rr55iHQ/viewform">à ces quelques questions</a>.</p>';
    	}elseif($note==4){
    		$html.='<p style="font-size:16px;">Merci pour votre retour! On a fait mieux que le guide Michelin ;-)</p>
            <p style="font-size:16px;">Faites découvrir Au Pas De Courses à vos amis et gagnez 10€ sur vos prochaines courses <a href="'.$url.'">en cliquant ici</a> !</p>';
    	} else{
            $html.='<p style="font-size:16px;">Merci pour votre retour! La prochaine fois, on visera la Lune ;-)</p>
            <p style="font-size:16px;">Faites découvrir Au Pas De Courses à vos amis et gagnez 10€ sur vos prochaines courses <a href="'.$url.'">en cliquant ici</a> !</p>';
        }

        $html.='</div>';

    	return $html;
    	
    }

}