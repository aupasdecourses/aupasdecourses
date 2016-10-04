<?php

/** Need to be recoded http://astrio.net/blog/magento-admin-form/ **/

class Apdc_Forms_IndexController extends Mage_Core_Controller_Front_Action
{

    public function getTransport(){
        return new Zend_Mail_Transport_Smtp('smtp.mandrillapp.com', array(
        'auth'     => 'login',
        'username' => 'pierre@aupasdecourses.com',
        'password' => 'suQMuVOzZHE5kc-wmH3oUA',
        'port'     => 587,
        ));
    }

    public function commercantsmanquantsAction()
    {
        //Get current layout state
        $this->loadLayout();

        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'pmainguet.commercant_form',
            array(
                'template' => 'forms/commercantform.phtml',
            )
        );

        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('head')->setTitle($this->__('Suggérez un commerce à Au Pas De Courses'));
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function produitsmanquantsAction()
    {
        //Get current layout state
        $this->loadLayout();

        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'pmainguet.products_form',
            array(
                'template' => 'forms/productform.phtml',
            )
        );

        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('head')->setTitle($this->__('Vous ne trouvez pas ce que vous cherchez ?'));
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function sendemailcommercantAction()
    {

        //Fetch submited params
        $params = $this->getRequest()->getParams();
        if ($params!=array()){
            $text = 'Expéditeur'.$params['name'].' - '.$params['email'].'&#10;Commentaire: '.$params['comment'].'&#10;Nom du commerçant: '.$params['nom-commercant'].'&#10;Adresse du commerçant: '.$params['adresse-commercant'].'&#10;Code Postal: '.$params['zipcode-commercant'];
            $html = '<p>Expéditeur'.$params['name'].' - '.$params['email'].'</p><p>Commentaire: '.$params['comment'].'</p><p>Nom du commerçant: '.$params['nom-commercant'].'</p><p>Adresse du commerçant: '.$params['adresse-commercant'].'</p><p>Code postal: '.$params['zipcode-commercant'].'</p>';

            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyText($text);
            $mail->setBodyHtml($html);
            $mail->setFrom('contact@aupasdecourses.com', 'Formulaire commerçants');
            $mail->addTo('contact@aupasdecourses.com', 'Au Pas De Courses');
            $mail->setSubject('Suggestion d\'un commerce pour Au Pas De Courses');
            try {
                $mail->send($this->getTransport());
                Mage::getSingleton('customer/session')->addSuccess('Votre message a bien été envoyé!');
            } catch (Exception $ex) {
                Mage::getSingleton('core/session')->addError('Impossible d\'envoyer la notification à Au Pas De Courses, merci de bien vouloir contacter l\'administrateur système.');
            }
        } else {
            Mage::getSingleton('core/session')->addError('Merci de renseigner l\'ensemble des champs du formulaire!');
        }

        $this->_redirect('formulaire/index/commercantsmanquants');
    }

    public function sendemailproduitsAction()
    {
        //Fetch submited params
        $params = $this->getRequest()->getParams();

        if ($params!=array()){
            $text = 'Expéditeur'.$params['name'].' - '.$params['email'].'&#10;Commentaire: '.$params['comment'].'&#10;Nom du ou des produits: '.$params['nom-produits'];
            $html = '<p>Expéditeur'.$params['name'].' - '.$params['email'].'</p><p>Commentaire: '.$params['comment'].'</p><p>Nom du ou des produits: '.$params['nom-produits'].'</p>';

            $mail = new Zend_Mail();
            $mail->setBodyText($text);
            $mail->setBodyHtml($html);
            $mail->setFrom('contact@aupasdecourses.com', 'Formulaire Produits');
            $mail->addTo('contact@aupasdecourses.com', 'Au Pas De Courses');
            $mail->setSubject('Produit(s) manquant(s) sur Au Pas De Courses');
            try {
                $mail->send($this->getTransport());
                Mage::getSingleton('customer/session')->addSuccess('Votre message a bien été envoyé!');
            } catch (Exception $ex) {
                Mage::getSingleton('core/session')->addError('Impossible d\'envoyer la notification à Au Pas De Courses, merci de bien vouloir contacter l\'administrateur système.');
            }
        } else {
            Mage::getSingleton('core/session')->addError('Merci de renseigner l\'ensemble des champs du formulaire!');
        }

        //Redirect back to index action this controller via frontname
        $this->_redirect('formulaire/index/produitsmanquants');
    }
}
