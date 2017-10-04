<?php
/*
* @author Pierre Mainguet
*/
class Apdc_Admin_Model_Mail extends Mage_Core_Model_Abstract
{
    protected $_transport;
    protected $_mail;

    public function __construct()
    {
        parent::__construct();

        $this->_mail = new Mandrill_Message(Mage::getStoreConfig(Ebizmarts_Mandrill_Model_System_Config::APIKEY));
        $this->_mail->addTo(['pierre@aupasdecourses.com'])
                    ->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'));
    }

    private function _warnAdmin($body, $subject)
    {
        $this->_mail->setSubject($subject);
        $this->_mail->setBodyHtml($body);
        try {
            $this->_mail->send($this->_transport);
        } catch (Exception $e) {
            Mage::log($e, null, 'email.log');
        }
    }

    public function warnErrorItemCommercant($error)
    {
        $subject = "Attention - Mistral Export - erreur d'id commercant dans des produits des commandes";
        $body = "Attention, le produits simple suivants sont présents dans des commandes qui sont en train d'être envoyés à Mistral.</br>Cependant ils possèdent des id commercants invalides, merci de bien vouloir corriger ce problème (cause possible: mauvais attribut commerçant associé à un shop):</br>";
        foreach ($error as $e) {
            $body .= '   - '.http_build_query($e, '', ', ').'</br>';
        }

        $this->_warnAdmin($body, $subject);
    }

    public function warnErrorCommercantNeighborhood($error)
    {
        $subject = 'Attention - Mistral Export - un commercant associé à une commande semble avoir été désactivé dans le quartier correspondant';
        $body = "Attention, le commercant proposant les produits suivants (intégré dans des commandes en train d'être envoyé à Mistral) semble avoir été désactivé du quartier où la commande a été passée.</br> Veuillez le réactiver et renvoyer les demandes d'enlèvement/livraison associées, car elles n'ont pas été transmises à Mistral:</br>";
        foreach ($error as $e) {
            $body .= '   - '.http_build_query($e, '', ', ').'</br>';
        }

        $this->_warnAdmin($body, $subject);
    }

    public function warnErrorMistral($error)
    {
        $subject = "Attention - Mistral Export - Une erreur s'est produite lors de l'export";
        $body = "Attention, une erreur s'est produite lors de l'envoi des commandes à Mistral.</br> Veuillez vérifier et corriger l'erreur avant de répétér l'opération.</br>Message d'erreur: ".$error;
        $this->_warnAdmin($body, $subject);
    }

    public function warnMistralDeactivated()
    {
        $subject = "Attention - Mistral Export - Une erreur s'est produite lors de l'export";
        $body = "L'export vers Mistral est désactivé mais vous avez pourtant tenté de lancer cet export. Veuillez vérifier votre configuration .";
        $this->_warnAdmin($body, $subject);
    }

    public function warnMailShopDeactivated()
    {
        $subject = "Attention - Envoi des commandes aux commerçants - Une erreur s'est produite lors de l'export";
        $body = "L'envoi de mail aux commerçants est désactivé mais vous avez pourtant tenté de lancer cet envoi. Veuillez vérifier votre configuration .";
        $this->_warnAdmin($body, $subject);
    }
}
