<?php

namespace Apdc\ApdcBundle\Services;

trait Credimemo
{
    /**
     * Retrieve order credit memo (refund) availability. 
     *
     * @return bool
     */
    private function canCreditmemo(\Mage_Sales_Model_Order $order)
    {
        if (!$order->getId()) {
            return false;
        }

        if ($order->hasForcedCanCreditmemo()) {
            return $order->getForcedCanCreditmemo();
        }

        if ($order->canUnhold()
            || $order->isPaymentReview()
            || $order->isCanceled()
            || $order->getState() === \Mage_Sales_Model_Order::STATE_CLOSED
            || abs($order->getStore()->roundPrice($order->getTotalPaid()) - $order->getTotalRefunded()) < .0001
            || $order->getActionFlag(\Mage_Sales_Model_Order::ACTION_FLAG_EDIT) === false) {
            return false;
        }

        return true;
    }

    /**
     * Initialize requested invoice instance or return false. 
     *
     * @return bool
     */
    private function checkinvoices(\Mage_Sales_Model_Order $order)
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
     * Check if order has credit memo or is complete, and return boolean to show or not buttons. 
     *
     * @param string $id, string $type ('creditmemo' or 'close')
     *
     * @return bool
     */
    public function checkdisplaybutton($id, $type)
    {
        $order = \Mage::getModel('sales/order')->loadbyIncrementId($id);
        if ($type == 'creditmemo') {
            return !$order->hasCreditmemos();
        } elseif ($type == 'close') {
            if (!$order->hasCreditmemos()) {
                return false;
            } else {
                return !($order->getStatus() == 'complete');
            }
        }
    }

    /**
     * Register Credit Memo Info in custom table for Facturation
     * Fusionner peut etre cette table custom avec table sales_creditmemo ?
     **/
    private function registerRefundorder($orderId, $commercant, $comment, $creditmemo)
    {
        $creditmemo_id = $creditmemo->getEntityId();

        $data = array(
            'order_id' => $orderId,
            'commercant' => $commercant['name'],
            'del_amount_refunded' => $commercant['refund_diff'],
            'comment' => $comment,
            'creditmemo_id' => $creditmemo_id,
        );

        $refund_order = \Mage::getModel('pmainguet_delivery/refund_order');

        //check if $creditmemo_id exist
        $check = $refund_order->getCollection()->addFieldToFilter(
            'creditmemo_id', [
                'in' => $creditmemo_id,
            ])->getFirstItem()->getId();
        if (is_null($check)) {
            $refund_order->setData($data);
            $refund_order->save();
        }
    }

    /**
     * Prepare order creditmemo based on order items and requested params (if there is no invoice).
     *
     * @param array $data
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    private function prepareCreditmemo($order, $data = array())
    {
        $totalQty = 0;
        $totalToRefund = 0;

        //set order, setStoreId, CustomerId, BillingAddressId, ShippingAddressId and fieldset in sales_flat_creditmemo
        $creditmemo = \Mage::getModel('sales/convert_order')->toCreditmemo($order);

        foreach ($order->getAllItems() as $orderItem) {
            //set order_item_id and product_id column in sales_flat_creditmemo_item (and copy fieldset between order and credit memo)
            $item = \Mage::getModel('sales/convert_order')->itemToCreditmemoItem($orderItem);

            $qty = 0;
            $totaltorefund = 0;

            if (isset($data['items'][$orderItem->getId()])) {
                $data_item = $data['items'][$orderItem->getId()];
                $qty = 0;
                $totaltorefund = floatval($data_item['refund']);
                $item->setData('amount_refunded', 1);
            } else {
                continue;
            }
            $totalToRefund += $totaltorefund;
            $creditmemo->addItem($item);
        }

        $creditmemo->setData('amount_refunded', $totalToRefund);

        $creditmemo->setBaseShippingAmount((float) $data['shipping_amount']);
        $creditmemo->setAdjustmentPositive($data['adjustment_positive']);
        $creditmemo->setAdjustmentNegative($data['adjustment_negative']);

        $creditmemo->collectTotals();

        return $creditmemo;
    }

    /**
     * Create credit memo.
     *
     * @param string $orderId, array $data
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    private function createcreditmemo($orderId, $data)
    {
        $order = \Mage::getSingleton('sales/order')->loadbyIncrementid($orderId);

        if (!$this->canCreditmemo($order)) {
            return;
        }

        $creditmemo = $this->prepareCreditmemo($order, $data);

        if (!empty($data['comment'])) {
            $creditmemo->addComment($data['comment'], false, false);
        }

        // Il sera peut etre intéressant de réaliser le remboursement automatiquement via Adyen en utilisant un Online request (à creuser)
        $creditmemo->setRefundRequested(true)->setOfflineRequested(true)->register();

        $transactionSave = \Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($order);

        if ($creditmemo->getInvoice()) {
            $transactionSave->addObject($creditmemo->getInvoice());
        }

        $transactionSave->save();

        return $creditmemo;
    }

    /**
     * save comment to Amasty Order Attach.
     **/
    private function registerorderattach($orderId, $order_concat)
    {
        $order_id = \Mage::getSingleton('sales/order')->loadByIncrementId($orderId)->getId();
        $field = \Mage::helper('pmainguet_delivery')->check_amorderattach($order_id);
        $field->setData('remboursements', $order_concat);
        $field->save();
    }

    /**
     * Create invoice.
     *
     * @return bool
     **/
    public function createinvoice($orderId)
    {
        $order = \Mage::getSingleton('sales/order')->loadbyIncrementid($orderId);
        $invoice = $this->checkinvoices($order);

        if (!$order->canInvoice() || !$invoice->getTotalQty()) {
            return false;
        }

        $invoice->setRequestedCaptureCase(\Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);
        $invoice->getOrder()->setIsInProcess(true);
        $order->addStatusHistoryComment('Automatically INVOICED.', false);
        $transactionSave = \Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        $transactionSave->save();

        return true;
    }

    /**
     * Prepare order creditmemo based on order items and requested params (if there is no invoice).
     * Return list of comment for each creditmemo.
     **/
    public function processcreditAction($orderId, $order)
    {
        $order_concat = [];
        foreach ($order as $merchant_id => $data) {
            if ($data['merchant']['refund_diff'] != 0.0) {
                $merchant_concat = [];
                foreach ($data['products'] as $product_id => $product) {
                    $merchant_concat[] = $product['refund_com'];
                }
                $merchant_concat = implode(' - ', $merchant_concat);
                $order_concat[$merchant_id] = "Ecart de {$data['merchant']['refund_diff']}€ pour {$data['merchant']['name']}.";
                $creditmemo_data = [
                    'merchant' => "{$data['merchant']['name']}",
                    'comment' => $order_concat[$merchant_id],
                    'items' => [],
                    'shipping_amount' => 0,
                    'adjustment_positive' => 0,
                    'adjustment_negative' => 0,
                ];
                if ($data['merchant']['refund_diff'] > 0) {
                    $creditmemo_data['adjustment_positive'] = $data['merchant']['refund_diff'];
                } else {
                    $creditmemo_data['adjustment_negative'] = -$data['merchant']['refund_diff'];
                }
                $creditmemo = $this->createcreditmemo($orderId, $creditmemo_data);

                if ($creditmemo->getId() != null) {
                    $this->registerRefundorder($orderId, $data['merchant'], $order_concat[$merchant_id], $creditmemo);
                }
            }
        }

        $order_concat = implode('<br />', $order_concat);
        $this->registerorderattach($orderId, $order_concat);

        return $order_concat;
    }

    /**
     * Prepare order creditmemo based on order items and requested params (if there is no invoice).
     * Return list of comment for each creditmemo.
     **/
    public function processcreditshipping($orderId, $refund_shipping)
    {
        $data = [
            'merchant' => 'Frais de livraison',
            'comment' => 'Remboursement des frais de livraison',
            'items' => [],
            'shipping_amount' => 0,
            'adjustment_positive' => $refund_shipping,
            'adjustment_negative' => 0,
        ];

        $creditmemo = $this->createcreditmemo($orderId, $data);

        if ($creditmemo->getId() != null) {
            $this->registerRefundorder($orderId, $data['merchant'], $data['comment'], $creditmemo);
        }
    }

    /**
     * Send email if credit memo.
     *
     * @param string $id, string $comment
     *
     * @return bool
     **/
    public function sendCreditMemoMail($orderId, $comment, $refund_diff, $refund_shipping)
    {
        $templateplus = 'delivery_emailcreditplus_template';
        $templatemoins = 'delivery_emailcreditmoins_template';
        $templatenull = 'delivery_emailcreditnull_template';

        if ($refund_diff > 0) {
            $templateId = $templateplus;
        } elseif ($refund_shipping < 0) {
            $templateId = $templatemoins;
        } elseif ($refund_diff = 0) {
            $templateId = $templatenull;
        }

        $sender = array(
            'name' => \Mage::getStoreConfig('trans_email/ident_general/name'),
            'email' => \Mage::getStoreConfig('trans_email/ident_general/email'),
        );

        $order = \Mage::getSingleton('sales/order')->loadByIncrementId($id);
        $nameTo = $order->getCustomerFirstname();
        $emailTo = $order->getCustomerEmail();
        $vars = array(
            'customer_firstname' => $nameTo,
            'order_id' => $orderId,
            'comment' => $comment,
            'refund_diff' => $refund_diff,
            'refund_shipping' => $refund_shipping,
            'refund_full' => $refund_diff + $refund_shipping,
        );

        $emailTemplate = \Mage::getSingleton('core/email_template');
        $emailTemplate->sendTransactional($templateId, $sender, $emailTo, $nameTo, $vars);

        return $emailTemplate->getSentSuccess();
    }

    /**
     * Close order.
     *
     * @param string $id
     *
     * @return bool
     **/
    public function setCloseStatus($orderId)
    {
        $order = \Mage::getSingleton('sales/order')->loadByIncrementId($orderId);
        $shipment = $order->prepareShipment();
        $shipment->register();
        $order->setIsInProcess(true);
        $order->addStatusHistoryComment('Automatically Shipped by APDC Delivery.', false);
        $transactionSave = \Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();
    }
}
