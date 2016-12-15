<?php

trait credimemo {
	private function canCreditmemo(\Mage_Sales_Model_Order $order)
	{
		if (!$order->getId())
			return false;

		if ($order->hasForcedCanCreditmemo())
			return $order->getForcedCanCreditmemo();

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

	private function createinvoice($orderId)
	{
		/*
		** @author Pierre Mainguet
		*/

		$order = \Mage::getModel('sales/order')->loadbyIncrementid($orderId);

		$invoice = \Mage::getModel('sales/service_order', $order)->prepareInvoice();


		if (!$order->canInvoice()) {
			throw new \Exception('Cannot create an invoice.');
		}

		if (!$invoice->getTotalQty()) {
			throw new \Exception('Cannot create an invoice without products.');
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
	}

	//Register Credit Memo Info in custom table for Facturation
	private function registerRefundorder($orderId, $commercant, $value, $creditmemo)
	{
		/*
		** @author Pierre Mainguet
		*/

		$creditmemo_id = $creditmemo->getEntityId();

		$data = array(
			'order_id' => $orderId,
			'commercant' => $commercant,
			'del_amount_refunded' => $value['value'],
			'comment' => $value['comment'],
			'creditmemo_id' => $creditmemo_id,
		);

		$refund_order = \Mage::getModel('pmainguet_delivery/refund_order');

		//check if $creditmemo_id exist
		$check = $refund_order->getCollection()->addFieldToFilter(
			'creditmemo_id', [
				'in' => $creditmemo_id
			])->addFieldToSelect('id')->getColumnValues('id');
		if (!is_null($check)) {
			$refund_order->setData($data);
			$refund_order->save();
		}
	}

	private function prepareCreditmemo($order, $data = array())
	{
		/*
		** @author Pierre Mainguet
		*/

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

//		$creditmemo->collectTotals();

		return $creditmemo;
	}

	private function createcreditmemo($orderId, $data)
	{
		/*
		** @author Pierre Mainguet
		*/

		$order = \Mage::getModel('sales/order')->loadbyIncrementid($orderId);

		$invoice = $this->checkinvoices($order);

		if (!$this->canCreditmemo($order)) {
			return null;
		}

		$creditmemo = $this->prepareCreditmemo($order, $data);

		if (!empty($data['comment'])) {
			$creditmemo->addComment($data['comment'], false, false);
		}

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

	//save comment to Amasty Order Attach
	private function registerorderattach($orderId, $order_concat)
	{
		$order_id = \Mage::getModel('sales/order')->loadByIncrementId($orderId)->getId();
		$field = \Mage::helper('pmainguet_delivery')->check_amorderattach($order_id);
		$field->setData('remboursements', $order_concat);
		$field->save();
	}

	public function processcreditAction($orderId, $order)
	{
		$this->createinvoice($orderId);

		$order_concat = [];
		foreach ($order as $merchant_id => $data) {
			if ($data['merchant']['refund_diff'] <> 0.0) {
				$merchant_concat = [];
				foreach ($data['products'] as $product_id => $product) {
					$merchant_concat[] = $product['refund_com'];
				}
				$merchant_concat = implode(' - ', $merchant_concat);
				$order_concat[$merchant_id] = "{$data['merchant']['name']}: {$data['merchant']['refund_diff']}€ -> {$merchant_concat}";
				$creditmemo_data = [
					'comment' => $order_concat[$merchant_id],
					'items' => [],
					'shipping_amount' => 0,
					'adjustment_positive' => 0,
					'adjustment_negative' => 0,
				];
				if ($data['merchant']['refund_diff'] > 0) {
					$creditmemo_data['adjustment_positive'] = $data['merchant']['refund_diff'];
				} else {
					$creditmemo_data['adjustment_negative'] = $data['merchant']['refund_diff'];
				}
				$creditmemo = $this->createcreditmemo($orderId, $creditmemo_data);
				if ($credimemo <> null)
					$this->registerRefundorder($orderId, $data['merchant']['name'], $data['merchant']['refund_diff'], $creditmemo);
			}
		}

		$order_concat = implode('<br />', $order_concat);
		$this->registerorderattach($orderId, $order_concat);
	}
}
