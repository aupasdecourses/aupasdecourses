<?php

class Pmainguet_Ajaxlp_IndexController extends Mage_Core_Controller_Front_Action {

    public function sendmailAction() { 
	  $params = $this->getRequest()->getParams();

	  $mail = new Zend_Mail();
	  $mail->setBodyText($params['message']);
	  $mail->setFrom($params['email'], $params['name']);
	  $mail->addTo('contact@aupasdecourses.com', 'Au Pas De Courses');
	  $mail->setSubject("'Un message vient d'être envoyé depuis la page d'accueil du site APDC");
	  try {
	      $mail->send();
	      echo "done";
	  }        
	  catch(Exception $ex) {
	      echo 'Impossible d\'envoyer la notification à Au Pas De Courses, merci de bien vouloir contacter l\'administrateur système'.'</br>';
	      echo $ex;
	        }

    }
}