<?php
 
class Pmainguet_CommercantForm_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //Get current layout state
        $this->loadLayout();   
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'pmainguet.commercant_form',
            array(
                'template' => 'commercantform/form.phtml'
            )
        );
 
        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('head')->setTitle($this->__('Suggérez un commerce à Au Pas De Courses'));
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
 
    public function sendemailAction()
    {
        //Fetch submited params
        $params = $this->getRequest()->getParams();

        $text='Commentaire: '.$params['comment'].'&#10;Nom du commerçant: '.$params['nom-commercant'].'&#10;Adresse du commerçant: '.$params['adresse-commercant'].'&#10;Code Postal: '.$params['zipcode-commercant'];
        $html='<p>Commentaire: '.$params['comment'].'</p><p>Nom du commerçant: '.$params['nom-commercant'].'</p><p>Adresse du commerçant: '.$params['adresse-commercant'].'</p><p>Code postal: '.$params['zipcode-commercant'].'</p>';
 
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($text);
        $mail->setBodyHtml($html);
        $mail->setFrom($params['email'], $params['name']);
        $mail->addTo('contact@aupasdecourses.com', 'Au Pas De Courses');
        $mail->setSubject('Suggestion d\'un commerce pour Au Pas De Courses');
        try {
            $mail->send();
            Mage::getSingleton('customer/session')->addSuccess('Votre message a bien été envoyé!');
        }        
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError('Impossible d\'envoyer la notification à Au Pas De Courses, merci de bien vouloir contacter l\'administrateur système.');
 
        }
 
        //Redirect back to index action this controller via frontname
        $this->_redirect('commercant-form/');
    }
}
 
?>