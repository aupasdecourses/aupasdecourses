<?php

/**
 *
 * @category   Apdc
 * @package    Apdc_Notation
 * @author     Pierre Mainguet
 */
class Apdc_Notation_Model_Autoresponder_Observer extends Ebizmarts_Autoresponder_Model_EventObserver
{

    public function orderSaved(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getOrder()->getStoreId();
        if(Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_ACTIVE, $storeId)) {


            $original_data = $observer->getEvent()->getData('data_object')->getOrigData();
            $new_data = $observer->getEvent()->getData('data_object')->getData();

            $order = $observer->getEvent()->getOrder();
            $configStatuses = explode(',',Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_ORDER_STATUS, $storeId));

            foreach($configStatuses as $status) {
                if (isset($new_data['status']) && isset($original_data['status']) && $original_data['status'] !== $new_data['status'] && $new_data['status'] == $status) {

                    if (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_ACTIVE, $storeId) && Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_TRIGGER, $storeId)) {


                        $tags = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_MANDRILL_TAG, $storeId) . "_$storeId";
                        $mailSubject = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_SUBJECT, $storeId);
                        $senderId = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::GENERAL_SENDER, $storeId);
                        $sender = array('name' => Mage::getStoreConfig("trans_email/ident_$senderId/name", $storeId), 'email' => Mage::getStoreConfig("trans_email/ident_$senderId/email", $storeId));
                        $templateId = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_TEMPLATE, $storeId);


                        //Send email
                        $translate = Mage::getSingleton('core/translate');
                        $email = $order->getCustomerEmail();
                        if (Mage::helper('ebizmarts_autoresponder')->isSubscribed($email, 'neworder', $storeId)) {
                            $name = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
                            $url = Mage::getModel('core/url')->setStore($storeId)->getUrl() . 'ebizautoresponder/autoresponder/unsubscribe?list=neworder&email=' . $email . '&store=' . $storeId;
                            
                            $ordernum=$order->getIncrementId();
                            $vars = array('tags' => array($tags), 'url' => $url, 'ordernum'=>$ordernum);
                            $mail = Mage::getModel('core/email_template')->setTemplateSubject($mailSubject)->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
                            $translate->setTranslateInLine(true);
                            Mage::helper('ebizmarts_abandonedcart')->saveMail('new order', $email, $name, "", $storeId);
                        }
                    }
                }
            }
        }

    }

}