<?php
 
class Pmainguet_ProductsForm_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //Get current layout state
        $this->loadLayout();   
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'pmainguet.products_form',
            array(
                'template' => 'productsform/form.phtml'
            )
        );
 
        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('head')->setTitle($this->__('Vous ne trouvez pas ce que vous cherchez ?'));
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
 
    public function sendemailAction()
    {
        //Fetch submited params
        $params = $this->getRequest()->getParams();
 
        $text='Commentaire: '.$params['comment'].'&#10;Nom du ou des produits: '.$params['nom-produits'];
        $html='<p>Commentaire: '.$params['comment'].'</p><p>Nom du ou des produits: '.$params['nom-produits'].'</p>';

        $mail = new Zend_Mail();
        $mail->setBodyText($text);
        $mail->setBodyHtml($html);
        $mail->setFrom($params['email'], $params['name']);
        $mail->addTo('contact@aupasdecourses.com', 'Au Pas De Courses');
        $mail->setSubject('Produit(s) manquant(s) sur Au Pas De Courses');
        try {
            $mail->send();
            Mage::getSingleton('customer/session')->addSuccess('Votre message a bien été envoyé!');
        }        
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError('Impossible d\'envoyer la notification à Au Pas De Courses, merci de bien vouloir contacter l\'administrateur système.');
 
        }
 
        //Redirect back to index action this controller via frontname
        $this->_redirect('products-form/');
    }
}
 
?>