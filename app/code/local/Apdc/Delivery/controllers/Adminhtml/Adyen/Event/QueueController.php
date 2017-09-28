<?php

require_once 'Adyen/Payment/controllers/Adminhtml/Adyen/Event/QueueController.php';

class Apdc_Delivery_Adminhtml_Adyen_Event_QueueController extends Adyen_Payment_Adminhtml_Adyen_Event_QueueController {

    protected $_debugData = array();
    
    private function _executeEventQueue($eventQueueId) {

        $eventQueue = Mage::getModel('adyen/event_queue')->load($eventQueueId);

        $eventQueue->getAdyenEventQueue();
        $incrementId = $eventQueue->getIncrementId();
        $varienObj = unserialize($eventQueue->getResponse());

        if(substr($incrementId,0,4)=="COM-"){
            $notif=Mage::getModel('adyen/processNotification');
            $notif->_declareCommonVariables($varienObj);
            $notif->_storeNotification();
            //$eventQueue->delete();
        }else{
            $orderExist = Mage::getResourceModel('adyen/order')->orderExist($incrementId);
            if (!empty($orderExist)) {
                $order = Mage::getModel('sales/order');
                $order->loadByIncrementId($incrementId);

                // process it
                $this->_debugData = Mage::getModel('adyen/processNotification')->updateOrder($order, $varienObj);

                // log it
                $this->_debug(null);

                // remove it from queue
                $eventQueue->delete();
            } else {
                // add this
                $currentAttempt = $eventQueue->getAttempt();
                $eventQueue->setAttempt(++$currentAttempt);
                $eventQueue->save();

                $this->_getSession()->addError($this->__('The order does not exist.'));
            }
        }
    }

}