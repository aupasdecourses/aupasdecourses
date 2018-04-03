<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_Model_Data_Sales 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Apdc
 * @uses     Apdc_Partner_Model_Data
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Data_Sales extends Apdc_Partner_Model_Data
{
    protected $filter = 'created_at';
    protected $sortOrder = 'DESC';

    public function getList()
    {
        return json_encode($this->getAllData());
    }

    protected function getAllData()
    {
        $invoices = $this->getInvoicesWithCreditmemos();
        $excludeOrderIds = $invoices->getColumnValues('order_id');
        $orders = $this->getOrders($excludeOrderIds);
        $quotes = $this->getQuotes();

        $allData = [];
        foreach ($invoices as $invoice) {
            $oneSale = [];
            $oneSale = [
                'status' => $invoice->getOrder()->getStatus(),
                'quote_id' => $invoice->getOrder()->getQuoteId(),
                'order_id' => $invoice->getOrder()->getId(),
                'invoice_id' => $invoice->getId(),
                'increment_id' => $invoice->getOrder()->getIncrementId(),
                'created_at' => $invoice->getOrder()->getCreatedAt(),
                'subtotal' => $invoice->getSubtotal(),
                'subtotal_incl_tax' => $invoice->getSubtotalInclTax(),
                'discount_amount' => $invoice->getDiscountAmount(),
                'tax_amount' => $invoice->getTaxAmount(),
                'shipping_tax_amount' => $invoice->getShippingTaxAmount(),
                'shipping_amount' => $invoice->getShippingAmount(),
                'grand_total' => $invoice->getGrandTotal(),
                'creditmemo_grand_total' => ($invoice->getCreditmemoGrandTotal() ? $invoice->getCreditmemoGrandTotal() : 0),
                'creditmemo_subtotal' => ($invoice->getCreditmemoSubtotal() ? $invoice->getCreditmemoSubtotal() : 0),
                'creditmemo_subtotal_incl_tax' => ($invoice->getCreditmemoSubtotalInclTax() ? $invoice->getCreditmemoSubtotalInclTax() : 0),
                'creditmemo_shipping_amount' => ($invoice->getCreditmemoShippingAmount() ? $invoice->getCreditmemoShippingAmount() : 0),
                'creditmemo_discount_amount' => ($invoice->getCreditmemoDiscountAmount() ? $invoice->getCreditmemoDiscountAmount() : 0),
                'creditmemo_tax_amount' => ($invoice->getCreditmemoTaxAmount() ? $invoice->getCreditmemoTaxAmount() : 0),
                'creditmemo_shipping_tax_amount' => ($invoice->getCreditmemoShippingTaxAmount() ? $invoice->getCreditmemoShippingTaxAmount() : 0),
                'creditmemo_adjustment' => ($invoice->getCreditmemoAdjustment() ? $invoice->getCreditmemoAdjustment() : 0),
            ];
            $oneSale['final_subtotal'] = $oneSale['subtotal'] - $oneSale['creditmemo_subtotal'];
            $oneSale['final_tax_amount'] = $oneSale['tax_amount'] - $oneSale['shipping_tax_amount'] - $oneSale['creditmemo_tax_amount'];

            $itemSubtotal = 0;
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getApdcPartnerId() == $this->getPartner()->getId()) {
                    $itemSubtotal += $item->getRowTotal();
                }
            }
            $oneSale['partner_items_subtotal'] = $itemSubtotal;

            $allData[$invoice->getData($this->filter)] = $oneSale;
        }
        foreach ($orders as $order) {
            $oneSale = [];
            $oneSale = [
                'status' => $order->getStatus(),
                'quote_id' => $order->getQuoteId(),
                'order_id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'created_at' => $order->getCreatedAt(),
                'subtotal' => $order->getSubtotal(),
                'subtotal_incl_tax' => $order->getSubtotalInclTax(),
                'discount_amount' => $order->getDiscountAmount(),
                'tax_amount' => $order->getTaxAmount(),
                'shipping_tax_amount' => $order->getShippingTaxAmount(),
                'shipping_amount' => $order->getShippingAmount(),
                'grand_total' => $order->getGrandTotal()
            ];
            $oneSale['final_subtotal'] = $oneSale['subtotal'];
            $oneSale['final_tax_amount'] = $oneSale['tax_amount'] - $oneSale['shipping_tax_amount'];

            $itemSubtotal = 0;
            foreach ($order->getAllItems() as $item) {
                if ($item->getApdcPartnerId() == $this->getPartner()->getId()) {
                    $itemSubtotal += $item->getRowTotal();
                }
            }
            $oneSale['partner_items_subtotal'] = $itemSubtotal;

            $allData[$order->getData($this->filter)] = $oneSale;
        }
        foreach ($quotes as $quote) {
            $totals = $quote->getShippingAddress();
            $oneSale = [];
            $oneSale = [
                'status' => Mage::helper('apdc_partner')->__('Panier créé'),
                'quote_id' => $quote->getId(),
                'created_at' => $quote->getCreatedAt(),
                'subtotal' => $totals->getSubtotal(),
                'subtotal_incl_tax' => $totals->getSubtotalInclTax(),
                'discount_amount' => $totals->getDiscountAmount(),
                'tax_amount' => $totals->getTaxAmount(),
                'shipping_tax_amount' => $totals->getShippingTaxAmount(),
                'shipping_amount' => $totals->getShippingAmount(),
                'grand_total' => $totals->getGrandTotal()
            ];
            $oneSale['final_subtotal'] = $oneSale['subtotal'];
            $oneSale['final_tax_amount'] = $oneSale['tax_amount'] - $oneSale['shipping_tax_amount'];

            $itemSubtotal = 0;
            foreach ($quote->getAllItems() as $item) {
                if ($item->getApdcPartnerId() == $this->getPartner()->getId()) {
                    $itemSubtotal += $item->getRowTotal();
                }
            }
            $oneSale['partner_items_subtotal'] = $itemSubtotal;

            $allData[$quote->getData($this->filter)] = $oneSale;
        }
        if ($this->sortOrder == 'DESC') {
            krsort($allData);
        } else {
            ksort($allData);
        }
        return $allData;
    }

    /**
     * getInvoicesWithCreditmemos
     *
     * @return Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    protected function getInvoicesWithCreditmemos()
    {
        $collection = Mage::getModel('sales/order_invoice')->getCollection()
            ->addFieldToFilter('main_table.apdc_partner_id', $this->getPartner()->getId());
        $this->addDateFilter($collection);

        $collection->getSelect()->joinLeft(
            ['creditmemo' => $collection->getTable('sales/creditmemo')],
            'creditmemo.order_id = main_table.order_id',
            [
                'creditmemo_grand_total' => 'SUM(creditmemo.grand_total)',
                'creditmemo_subtotal' => 'SUM(creditmemo.subtotal)',
                'creditmemo_subtotal_incl_tax' => 'SUM(creditmemo.subtotal_incl_tax)',
                'creditmemo_shipping_amount' => 'SUM(creditmemo.shipping_amount)',
                'creditmemo_discount_amount' => 'SUM(creditmemo.discount_amount)',
                'creditmemo_tax_amount' => 'SUM(creditmemo.tax_amount)',
                'creditmemo_shipping_tax_amount' => 'SUM(creditmemo.shipping_tax_amount)',
                'creditmemo_adjustment' => 'SUM(creditmemo.adjustment)'
            ]
        );
        $collection->getSelect()->group('main_table.entity_id');

        $data = $this->getSalesData();
        if (isset($data['quote_ids']) && !empty($data['quote_ids'])) {
            $quoteIds = explode(',', $data['quote_ids']);
            $collection->getSelect()->join(
                ['orders' => $collection->getTable('sales/order')],
                'orders.entity_id = main_table.order_id',
                ''
            );
            $collection->getSelect()->where('orders.quote_id in (?)', $quoteIds);
        }

        return $collection;
    }

    /**
     * getOrders
     * 
     * @param array[int] $excludeIds excludeIds 
     * 
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function getOrders($excludeIds)
    {
        $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('main_table.apdc_partner_id', $this->getPartner()->getId());
        $this->addDateFilter($collection);
        if (!empty($excludeIds)) {
            $collection->addFieldToFilter('entity_id', ['nin' => $excludeIds]);
        }

        $data = $this->getSalesData();
        if (isset($data['quote_ids']) && !empty($data['quote_ids'])) {
            $quoteIds = explode(',', $data['quote_ids']);
            $collection->addFieldToFilter('quote_id', ['in' => $quoteIds]);
        }


        return $collection;
    }

    /**
     * getQuotes
     * 
     * @return Mage_Sales_Model_Resource_Quote_Collection
     */
    protected function getQuotes()
    {
        $collection = Mage::getModel('sales/quote')->getCollection()
            ->addFieldToFilter('main_table.apdc_partner_id', $this->getPartner()->getId());
        $this->addDateFilter($collection);

        // exclude quote that have been ordered already
        $collection->getSelect()->joinLeft(
            ['orders' => $collection->getTable('sales/order')],
            'orders.quote_id = main_table.entity_id',
            []
        );
        $collection->getSelect()->where('orders.entity_id IS NULL');


        $data = $this->getSalesData();
        if (isset($data['quote_ids']) && !empty($data['quote_ids'])) {
            $quoteIds = explode(',', $data['quote_ids']);
            $collection->addFieldToFilter('main_table.entity_id', ['in' => $quoteIds]);
        }

        return $collection;
    }

    protected function addDateFilter(&$collection)
    {
        $data = $this->getSalesData();
        if (isset($data['from']) && !empty($data['from'])) {
            $collection->addFieldToFilter('main_table.created_at', ['from' => $data['from']]);
        }
        if (isset($data['to']) && !empty($data['to'])) {
            $collection->addFieldToFilter('main_table.created_at', ['to' => $data['to']]);
        }
    }
}
