<?php

class Pmainguet_Delivery_CreditController extends Mage_Core_Controller_Front_Action {

//     public function indexAction(){}

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        if ($this->hasForcedCanCreditmemo()) {
            return $this->getForcedCanCreditmemo();
        }

        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->isCanceled() || $this->getState() === self::STATE_CLOSED) {
            return false;
        }

        /**
         * We can have problem with float in php (on some server $a=762.73;$b=762.73; $a-$b!=0)
         * for this we have additional diapason for 0
         * TotalPaid - contains amount, that were not rounded.
         */
        if (abs($this->getStore()->roundPrice($this->getTotalPaid()) - $this->getTotalRefunded()) < .0001) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_EDIT) === false) {
            return false;
        }
        return true;
    }


    /**
     * Check if creditmeno can be created for order
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function _canCreditmemo($order)
    {
        /**
         * Check order existing
         */
        if (!$order->getId()) {
            return 'The order no longer exists.';
        }

        // *
        //  * Check creditmemo create availability
        
        if (!$order->canCreditmemo()) {
            return 'Cannot create credit memo for the order.';
        }
        return true;
    }

    /**
     * Initialize requested invoice instance
     * @param unknown_type $order
     */
    protected function checkinvoices($order)
    {

        if ($order->hasInvoices()) {
            $invoiceids = array();
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoiceids[] = $invoice->getIncrementId();
            }
            return $invoiceids;
        }

        return false;
    }

    /**
     * Prepare order creditmemo based on order items and requested params (if there is no invoice)
     *
     * @param array $data
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function prepareCreditmemo($order,$data = array())
    {
        $totalQty = 0;
        $totalToRefund=0;

        //set order, setStoreId, CustomerId, BillingAddressId, ShippingAddressId and fieldset in sales_flat_creditmemo
        $creditmemo = Mage::getModel('sales/convert_order')->toCreditmemo($order);

        foreach ($order->getAllItems() as $orderItem) {
            //set order_item_id and product_id column in sales_flat_creditmemo_item (and copy fieldset between order and credit memo)
            $item = Mage::getModel('sales/convert_order')->itemToCreditmemoItem($orderItem);

            $qty=0;
            $totaltorefund=0;
            
            if (isset($data['items'][$orderItem->getId()])) {
                $data_item=$data['items'][$orderItem->getId()];
                $qty=0;
                $totaltorefund=floatval($data_item['refund']);
                $item->setData('amount_refunded',1);
            } else {
                continue;
            }
            $totalToRefund += $totaltorefund;
            $creditmemo->addItem($item);
        }

        $creditmemo->setData('amount_refunded',$totalToRefund);

        if (isset($data['shipping_amount'])) {
             $creditmemo->setBaseShippingAmount((float)$data['shipping_amount']);
        }

        if (isset($data['adjustment_positive'])) {
            $creditmemo->setAdjustmentPositive($data['adjustment_positive']);
        }

        if (isset($data['adjustment_negative'])) {
            $creditmemo->setAdjustmentNegative($data['adjustment_negative']);
        }

        $creditmemo->collectTotals();
        return $creditmemo;
    }

    public function processcreditAction(){

        $orderId = $this->getRequest()->getParam('order_id');
        $total   = $this->getRequest()->getParam('total');
        $data   = $this->getRequest()->getParam('data');
        $creditmemo=array(
            'items'=>array(),
            'shipping_amount' => 0,
            //ajustement du TTC = HT
            // 'adjustment_positive' => floatval($total['total_remboursement']),
            'adjustment_positive' => 0,
            //ajustement des taxes (pas pris en compte)
            'adjustment_negative' => 0,
        );

        //get total to refund per commercant
        $totalrefund=array();
        foreach($data as $row){
            if(array_key_exists($row['commercant'],$totalrefund)){
                $totalrefund[$row['commercant']]['value']+=floatval($row['diffprixfinal']);
                if(!in_array($row['comment'],array("",NULL))){
                    $totalrefund[$row['commercant']]['comment'].=$row['comment'].' - ';
                }
            }else{
                $totalrefund[$row['commercant']]['value']=floatval($row['diffprixfinal']);
                if(!in_array($row['comment'],array("",NULL))){
                    $totalrefund[$row['commercant']]['comment']=$row['comment'].' - ';
                }else{
                    $totalrefund[$row['commercant']]['comment']='';
                }
            }
        }

        //remove 0 value
        foreach($totalrefund as $k=>$v){
            if($v['value']==0){
                unset($totalrefund[$k]);
            }
        }

        //message array
        $msg=array();     

        try{
            $this->createinvoice($orderId);
            $msg['invoice']="Facture créée.";
        }catch(Exception $e){
            $msg['invoice']="Facture non créée/déjà existante.";
        }

        try{       

            //creation d'un credit memo par commercant
            foreach($totalrefund as $k=>$v){
                $comment=$k.' pour '.$v['value'].'€ ';
                if(!in_array($v['comment'],array("",NULL))){
                    $comment.=' pour les raisons suivantes: '.$v['comment'];
                }

                $creditmemo_data=array(
                'comment'=>$comment,
                'items'=>array(),
                'shipping_amount' => 0,
                //ajustement du TTC = HT
                'adjustment_positive' => $v['value'],
                //ajustement des taxes (pas pris en compte)
                'adjustment_negative' => 0,
                );

                //Create Credit Memo in Magento database
                $creditmemo=$this->createcreditmemo($orderId,$creditmemo_data);

                //Register Credit Memo Info in custom table for Facturation
                $this->registerRefundorder($orderId,$k,$v,$creditmemo);

                $msg[$k]="Crédit mémo créé pour ".$k;
            }

            //save comment to Amasty Order Attach
            $this->registerorderattach($orderId,$totalrefund);

            //return messages to ajax call
            echo json_encode($msg);

        } catch (Mage_Core_Exception $e) {
                 echo $e->getMessage();
        }

    }

    //save comment to Amasty Order Attach
    public function registerorderattach($orderId,$totalrefund){
       if(count($totalrefund)>0){
                $comment_order="Remboursements effectués: ";
                foreach($totalrefund as $k => $v){
                    $comment_order.=$k.' pour '.$v['value'].'€ ';
                    if(!in_array($v['comment'],array("",NULL))){
                        $comment_order.=' pour les raisons suivantes: '.$v['comment'].',';
                    }
                }
                $order_id=Mage::getModel('sales/order')->loadByIncrementId($orderId)->getId();
                $field=Mage::helper('pmainguet_delivery')->check_amorderattach($order_id);
                $field->setData('remboursements',$comment_order);
                $field->save();
        }
    }

    //Register Credit Memo Info in custom table for Facturation
    public function registerRefundorder($orderId,$commercant,$value,$creditmemo){
        
        $creditmemo_id=$creditmemo->getEntityId();

        try{
            $data=array(
                'order_id'=>$orderId,
                'commercant'=>$commercant,
                'commercant_id'=>'',
                'final_row_total'=>'',
                'del_amount_refunded'=>$value['value'],
                'del_tax_refunded'=>'',
                'comment'=>$value['comment'],
                'creditmemo_id'=>$creditmemo_id,
            );

            $refund_order = Mage::getModel('pmainguet_delivery/refund_order');

            //check if $creditmemo_id exist
            $check=$refund_order->getCollection()->addFieldToFilter('creditmemo_id', array('in' => $creditmemo_id))->addFieldToSelect('id')->getColumnValues('id');
            if(!is_null($check)){
                $refund_order->setData($data);
                $refund_order->save();
                echo "Refund Order saved.";
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }

    }


    public function createinvoice($orderId){
        
        $order  = Mage::getModel('sales/order')->loadbyIncrementid($orderId);

        if(!$order->canInvoice())
        {
        Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
        }
         
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
         
        if (!$invoice->getTotalQty()) {
        Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
        }
         
        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);          
        $invoice->getOrder()->setIsInProcess(true);
        $order->addStatusHistoryComment('Automatically INVOICED.', false);
        $transactionSave = Mage::getModel('core/resource_transaction')
        ->addObject($invoice)
        ->addObject($invoice->getOrder());
         
        $transactionSave->save();

    }

    public function createcreditmemo($orderId,$data){

        $order  = Mage::getModel('sales/order')->loadbyIncrementid($orderId);

        //Check if invoice exist
        $invoice = $this->checkinvoices($order);

        // //Check if can create credit memo: check if hasForcedCanCreditmemo, if it's not hold, in payment review, canceled or closed, if it's not already totally refunded, or is edited
        if (!$this->_canCreditmemo($order)) {
            return false;
        }

        //Prepare order creditmemo based on invoice items and requested requested params => check if qty refunded OK
        // if ($invoice) {
        //     $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $creditmemo);
        // } else {
            $creditmemo = $this->prepareCreditmemo($order,$data);
        //}

        // $orderCreditMemoStatusCode = 'processing';
        // $orderCreditMemoStatusComment = $creditmemo['comment'];
        //$saveTransaction = Mage::getModel('core/resource_transaction')->addObject ($creditmemo )->addObject ( $order )->save ();
        // $order->addStatusToHistory ( $orderCreditMemoStatusCode, $orderCreditMemoStatusComment, true );

        $notifyCustomer = true;
        $visibleOnFront=false;
        $includeComment=true;
        $comment = $data['comment'];

        // add comment to creditmemo
        if (!empty($comment)) {
            $creditmemo->addComment($comment, $notifyCustomer,$visibleOnFront);
        }

        try {
            //  
            $creditmemo->setRefundRequested(true)->setOfflineRequested(true)->register();
            
            //save credit memo and order and invoice
            $transactionSave=Mage::getModel('core/resource_transaction')
                ->addObject($creditmemo)
                ->addObject($order);

            if ($creditmemo->getInvoice()) {
                $transactionSave->addObject($creditmemo->getInvoice());
            }
            
            $transactionSave->save();
            // send email notification
            Mage::log('Email sent: '.$comment, null, 'email.log');
            $creditmemo->sendEmail($notifyCustomer, ($includeComment ? $comment : ''));

        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $creditmemo;

    }
}