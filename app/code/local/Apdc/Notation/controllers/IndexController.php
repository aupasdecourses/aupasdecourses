<?php
/**
 * @author Pierre Mainguet
 * @copyright Copyright (c) 2016 Pierre Mainguet - mainguetpierre@gmail.com
 */
class Apdc_Notation_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function surveyAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function noteAction()
    {
        $request = $this->getRequest();
        $orderId = $request->getParam('order_id');
        $note = $request->getParam('note');

        if (isset($orderId) && isset($note) && $orderId != null && $note != null) {
            if (!Mage::helper('apdc_notation')->noteExists($orderId)) {
                $notationClient = Mage::getSingleton('apdc_notation/notation');
                $notationClient->setOrderId($orderId);
                if ($note < 1 || $note > 5) {
                    $note = 0;
                }
                $notationClient->setNote($note);
                $notationClient->save();

                try {
                    $order = Mage::getSingleton('sales/order')->load($orderId);
                    $increment_id = $order->getIncrementId();
                    $customer_name = $order->getCustomerFirstname().' '.$order->getCustomerLastname();

                    $templateId = 'notification_new_note';
                    $sender = array(
                        'name' => Mage::getStoreConfig('trans_email/ident_general/name'),
                        'email' => Mage::getStoreConfig('trans_email/ident_general/email'),
                    );
                    $vars = array(
                        'order_id' => $orderId,
                        'increment_id' => $increment_id,
                        'customer_name' => $customer_name,
                        'note' => $note,
                        'url_note' => Mage::getBaseUrl().'../indi/web/stat/noteOrder/',
                    );
                    $emailTemplate = Mage::getSingleton('core/email_template');
                    $emailTemplate->sendTransactional($templateId, $sender, 'notification@aupasdecourses.com', 'Equipe Au Pas De Courses', $vars);
                } catch (Exception $e) {
                    Mage::log($e, null, 'newsletter.log');
                }
            } else {
                $notationClient = Mage::getSingleton('apdc_notation/notation');
                $notationClient->load($orderId, 'order_id');
                $note = $notationClient->getNote();
            }
            $refererUrl = Mage::getUrl('votrecommande/index?note='.$note);
        } else {
            $refererUrl = Mage::getUrl();
        }

        $this->getResponse()->setRedirect($refererUrl);

        return $this;
    }
}
