<?php

class Apdc_Delivery_Model_Adyen_ProcessNotification extends Adyen_Payment_Model_ProcessNotification {

    /**
     * MODIFIED SO WE DON'T SEND DUPLICATE EMAIL ON ORDER CREATION, ONLY ON ORDER CREATION
     * @param $order
     * @param $payment_method
     */ 
    protected function _authorizePayment($order, $payment_method)
    {
        $this->_debugData[$this->_count]['_authorizePayment'] = 'Authorisation of the order';

        $this->_uncancelOrder($order);

        $fraudManualReviewStatus = $this->_getFraudManualReviewStatus($order);

        // If manual review is active and a seperate status is used then ignore the pre authorized status
        if($this->_fraudManualReview != true || $fraudManualReviewStatus == "") {
            $this->_setPrePaymentAuthorized($order);
        } else {
            $this->_debugData[$this->_count]['_authorizePayment info'] = 'Ignore the pre authorized status because the order is under manual review and use the Manual review status';
        }

        $this->_prepareInvoice($order);

        $_paymentCode = $this->_paymentMethodCode($order);

        // for boleto confirmation mail is send on order creation
        //if($payment_method != "adyen_boleto") {
            // send order confirmation mail after invoice creation so merchant can add invoicePDF to this mail
        //    $order->sendNewOrderEmail(); // send order email
        //}

        if(($payment_method == "c_cash" && $this->_getConfigData('create_shipment', 'adyen_cash', $order->getStoreId())) || ($this->_getConfigData('create_shipment', 'adyen_pos', $order->getStoreId()) && $_paymentCode == "adyen_pos"))
        {
            $this->_createShipment($order);
        }
    }

    /**
     * ADD CASE FOR THIRDPARTY PAYOUT => STORE THEM IN ADYEN_EVENT TABLE FOR USE IN INDI
     *
     */
    protected function _updateNotProcessedNotifications() {

        $this->_debugData['UpdateNotProcessedEvents Step1'] = 'Going to update Notifications from the queue';

        // try to update old notifications that did not processed yet
        $collection = Mage::getModel('adyen/event_queue')->getCollection()
            ->addFieldToFilter('attempt', array('lteq' => '4'))
            ->addFieldToFilter('created_at', array(
                'from' => strtotime('-1 day', time()),
                'to' => strtotime('-1 minutes', time()),
                'datetime' => true))
            ->addOrder('created_at', 'asc');


        $limit = (int)$this->_getConfigData('event_queue_limit');
        if ($limit > 0) {
            $collection->getSelect()->limit($limit);
        }

        if($collection->getSize() > 0) {
            $this->_count = 0;
            foreach($collection as $event){

                $incrementId = $event->getIncrementId();
                $params = unserialize($event->getResponse());

                // If the event is a RECURRING_CONTRACT wait an extra 5 minutes before processing so we are sure the RECURRING_CONTRACT
                if (trim($params->getData('eventCode')) == Adyen_Payment_Model_Event::ADYEN_EVENT_RECURRING_CONTRACT &&
                    strtotime($event->getCreatedAt()) >= strtotime('-5 minutes', time())) {
                    $this->_debugData[$this->_count]['UpdateNotProcessedEvents end'] = 'This is a recurring_contract notification wait an extra 5 minutes before processing this to make sure the contract exists';
                    $this->_count++;
                    continue;
                }

                $this->_debugData[$this->_count]['UpdateNotProcessedEvents Step2'] = 'Going to update notification with incrementId: ' . $incrementId;

                $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
                if ($order->getId()) {

                    $this->_debugData[$this->_count]['UpdateNotProcessedEvents Step3'] = 'Order exists going to update it';
                    // try to process it now
                    $this->_debugData[$this->_count]['UpdateNotProcessedEvents params'] = $params->debug();

                    // check if notification is already processed
                    if(!$this->_isDuplicate($params)) {
                        try {
                            $this->_updateOrder($order, $params);
                        } catch (\Exception $error) {
                            $this->_debugData[$this->_count]['UpdateNotProcessedEvents updateOrderException']  = $error->getMessage();
                            Mage::logException($error);
                        }
                    } else {
                        // already processed so ignore this notification
                        $this->_debugData[$this->_count]['UpdateNotProcessedEvents duplicate']  = "This notification is already processed so ignore this one";
                    }

                    // update event that it is processed
                    try{
                        // @codingStandardsIgnoreStart
                        $event->delete();
                        // @codingStandardsIgnoreEnd
                        $this->_debugData[$this->_count]['UpdateNotProcessedEvents Step4'] = 'Notification is processed and removed from the queue';
                    } catch(Exception $e) {
                        Mage::logException($e);
                    }
                } elseif($adyenEventCode==Adyen_Payment_Model_Event::ADYEN_EVENT_RECURRING_CONTRACT||$adyenEventCode==Adyen_Payment_Model_Event::ADYEN_EVENT_PAYOUT_THIRDPARTY){
                    //Added to process PAYOUT_TOTHIRD_PARTY_EVENT into adyen_event table
                    $this->_storeNotification();
                }else {
                    // order does not exists remove this from the queue
                    // @codingStandardsIgnoreStart
                    $event->delete();
                    // @codingStandardsIgnoreEnd
                    $this->_debugData[$this->_count]['UpdateNotProcessedEvents Step3'] = 'The Notification still does not exists so it does not have an order remove the notification';
                }

                $this->_count++;
            }
        } else {
            $this->_debugData['UpdateNotProcessedEvents Step2'] = 'The queue is empty';
        }
    }

}
