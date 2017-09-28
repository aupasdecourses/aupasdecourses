<?php

require_once 'Adyen/Payment/controllers/Adminhtml/Adyen/Event/QueueController.php';

class Apdc_Delivery_Adminhtml_Adyen_Event_QueueController extends Adyen_Payment_Adminhtml_Adyen_Event_QueueController {

    protected $_debugData = array();
    
    /**
     * This tries to process the notification again
     */
    public function executeAction() {
        // get event queue id
        $eventQueueId = $this->getRequest()->getParam('event_queue_id');
        $this->_executeEventQueuenew($eventQueueId);

        // return back to the view
        $this->_redirect('*/*/');
    }

    public function massExecuteAction()
    {
        $queueIds = $this->getRequest()->getParam('queue_id');      // $this->getMassactionBlock()->setFormFieldName('queue_id'); from Adyen_Payment_Block_Adminhtml_Adyen_Event_Queue_Grid

        if(!is_array($queueIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adyen')->__('Please select notification queue(s).'));
        } else {
            try {
                $eventQueueModel = Mage::getModel('adyen/event_queue');
                foreach ($queueIds as $queueId) {
                    $this->_executeEventQueuenew($queueId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adyen')->__(
                        'Total of %d record(s) were deleted.', count($queueIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    private function _executeEventQueuenew($eventQueueId) {

        $eventQueue = Mage::getModel('adyen/event_queue')->load($eventQueueId);

        $eventQueue->getAdyenEventQueue();
        $incrementId = $eventQueue->getIncrementId();
        $varienObj = unserialize($eventQueue->getResponse());

        if(substr($incrementId,0,4)=="COM-"){
            $notif=Mage::getModel('adyen/processNotification')->storeNotificationPayout($varienObj);
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