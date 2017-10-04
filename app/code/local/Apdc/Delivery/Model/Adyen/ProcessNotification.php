<?php

class Apdc_Delivery_Model_Adyen_ProcessNotification extends Adyen_Payment_Model_ProcessNotification {

    public function storeNotificationPayout($varienObj){
        if(!$this->_isDuplicate($varienObj)) {
            $this->_declareCommonVariables($varienObj);
            $this->_storeNotification();
        }
    }

    /**
     * SAME AS ORIGINAL, COPY BECAUSE IT IS A PRIVATE FUNCTION
     * 
     * @param $order
     */
    private function _setPrePaymentAuthorized($order)
    {
        $status = $this->_getConfigData('payment_pre_authorized', 'adyen_abstract', $order->getStoreId());

        // only do this if status in configuration is set
        if(!empty($status)) {

            $statusObject = Mage::getModel('sales/order_status')->getCollection()
                ->addFieldToFilter('main_table.status', $status)
                ->addFieldToFilter('state_table.is_default', true)
                ->joinStates()
                ->getFirstItem();
            $state = $statusObject->getState();
            $order->setState($state, $status, Mage::helper('adyen')->__('Payment is pre authorised waiting for capture'));

            /**
             * save the order this is needed for older magento version so that status is not reverted to state NEW
             */
            $order->save();
            $order->sendOrderUpdateEmail((bool) $this->_getConfigData('send_update_mail', 'adyen_abstract', $order->getStoreId()));
            $this->_debugData[$this->_count]['_setPrePaymentAuthorized'] = 'Order status is changed to Pre-authorised status, status is ' . $status;
        } else {
            $this->_debugData[$this->_count]['_setPrePaymentAuthorized'] = 'No pre-authorised status is used so ignore';
        }
    }

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

        //for boleto confirmation mail is send on order creation
        if($payment_method != "adyen_boleto" && !$order->getEmailSent()) {
           //send order confirmation mail after invoice creation so merchant can add invoicePDF to this mail
           $order->sendNewOrderEmail(); // send order email
        }

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
                } elseif(trim($params->getData('eventCode'))==Adyen_Payment_Model_Event::ADYEN_EVENT_RECURRING_CONTRACT||trim($params->getData('eventCode'))==Adyen_Payment_Model_Event::ADYEN_EVENT_PAYOUT_THIRDPARTY){
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
