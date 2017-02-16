<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  SalesRule
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_SalesRule_Model_Observer 
 * 
 * @category Apdc
 * @package  SalesRule
 * @uses     Mage
 * @uses     Mage_SalesRule_Model_Observer
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_SalesRule_Model_Observer extends Mage_SalesRule_Model_Observer
{
    /**
     * Registered callback: called after an order is placed
     *
     * @param Varien_Event_Observer $observer
     */
    public function sales_order_afterPlace($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        // Test if it is a new invoice
        if ($invoice->getUpdatedAt() == $invoice->getCreatedAt()) {
            $order = $invoice->getOrder();
            if ($order && $order->getId() > 0) {
                $observer->getEvent()->setData('order', $order);
            }
            parent::sales_order_afterPlace($observer);
        }
    }
}
