<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * GardenMedia_Sponsorship_Model_Observer 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Model_Observer
{
    /**
     * checkAfterInvoice
     * sales_order_invoice_save_after
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function checkAfterInvoice(Varien_Event_Observer $observer)
    {
        if (Mage::helper('gm_sponsorship')->isEnabled()) {
            $invoice = $observer->getEvent()->getInvoice();
            // Only when invoice is created
            if ($invoice->getCreatedAt() == $invoice->getUpdatedAt()) {
                $order = $invoice->getOrder();
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $this->checkRewards($customer, $invoice);
            }
        }
    }

    /**
     * checkRewards 
     * 
     * @param Mage_Customer_Model_Customer   $customer : magento customer entity
     * @param Mage_Sales_Model_Order_Invoice $invoice  : Magento invoice entity
     * 
     * @return void
     */
    protected function checkRewards($customer, $invoice)
    {
        $godchild = Mage::getModel('gm_sponsorship/godchild')->load($customer->getId());
        if ($godchild && $godchild->getId() > 0) {
            $minimumOrder = Mage::getStoreConfig('gm_sponsorship/rewards/minimum_ordered_amount');

            $rewards = Mage::getModel('gm_sponsorship/salesrule_coupon_customer')->getCollection()
                ->addFieldToFilter('customer_owner_id', $customer->getId())
                ->addFieldToFilter('customer_linked_id', array('neq' => $customer->getId()));
            if ($rewards->count() == 0) {
                $totalOrdered = Mage::helper('gm_sponsorship')->getTotalAmountOrdered($customer);
                if ($totalOrdered->count() > 0) {
                    $totalOrdered = $totalOrdered->getFirstItem();
                    $totalAmount = $totalOrdered->getTotalInvoiced() - $totalOrdered->getTotalRefunded();
                    if ($totalOrdered->getInvoiceId() != $invoice->getId()) {
                        $totalAmount += $invoice->getBaseGrandTotal();
                    }
                } else {
                    $totalAmount = $invoice->getBaseGrandTotal();
                }
                if ($totalAmount >= $minimumOrder) {
                    Mage::helper('gm_sponsorship')->createReward($customer, $godchild->getSponsorId());
                }
            }
        }
    }

    /**
     * postDispatchInvitation 
     * controller_action_predispatch
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function preDispatchInvitation(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $session = Mage::getSingleton('core/session');
        if ($session->getSponsorData()) {
            $refererUrl = Mage::helper('core/http')->getHttpReferer();
            if ($refererUrl && $refererUrl == Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)) {
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
                $path = str_replace('/', '', $url->getPath());
                $stores = Mage::app()->getStores(false, true);
                if (isset($stores[$path])) {
                    $action->setFlag('', $action::FLAG_NO_PRE_DISPATCH, true);
                    Mage::app()->getResponse()->setRedirect($stores[$path]->getUrl('customer/account/create'));
                }
            }
            if ($action->getFullActionName() == 'customer_account_create') {
                $sponsorName = $session->getSponsorData()->getSponsor()->getName();
                $message = sprintf(Mage::getStoreConfig('gm_sponsorship/general/register_sponsor_message'), $sponsorName);
                Mage::getSingleton('customer/session')->addSuccess($message);
            }
        }
        if ($action->getFullActionName() == 'checkout_cart_couponPost') {
            $couponCode = (string) $action->getRequest()->getParam('coupon_code');
            $customerId = (int) Mage::getSingleton('customer/session')->getId();
            if (strlen($couponCode) && $customerId > 0) {
                $coupons = Mage::getModel('salesrule/coupon')->getCollection()
                    ->addFieldToFilter('code', $couponCode);

                $coupons->getSelect()->joinLeft(
                    array('coupon_customer' => $coupons->getTable('gm_sponsorship/salesrule_coupon_customer')),
                    'coupon_customer.coupon_id = main_table.coupon_id',
                    array('customer_owner_id')
                );

                if ($coupons->count() > 0) {
                    $coupon = $coupons->getFirstItem();

                    if ($coupon->getCustomerUnique() && (int)$coupon->getCustomerOwnerId() != (int)$customerId) {
                        $action->getRequest()->setParam('coupon_code', '');
                        Mage::getSingleton('checkout/session')->addError(
                            Mage::helper('checkout')->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                        );
                    }
                }
            }
        }
    }

    /**
     * associateSponsorAndGodchild 
     * customer_register_success
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function associateSponsorAndGodchild(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('core/session');
        $customer = $observer->getEvent()->getCustomer();
        $sponsorData = $session->getSponsorData();
        if ($sponsorData && !empty($sponsorData)) {
            $godchild = Mage::getModel('gm_sponsorship/godchild')
                ->setGodchildId($customer->getId())
                ->setSponsorId($sponsorData->getSponsor()->getId());
                $godchild->save();

            $sponsor = $session->getSponsorData()->getSponsor();
            $message = sprintf(Mage::getStoreConfig('gm_sponsorship/general/register_success_message'), $sponsor->getName());
            Mage::getSingleton('core/session')->addSuccess($message);

            // Send new godchild email to sponsor
		    $emailTemplate = Mage::getModel('core/email_template');
            $templateId = Mage::getStoreConfig('gm_sponsorship/sponsor/template_new_godchild');
            if (!$templateId) {
                $templateId = 'gm_sponsorship_sponsor_template_new_godchild';
            }
            $vars = array(
                'sponsor' => $sponsor,
                'godchild' => $customer
            );
            $sender = array(
                'name' => Mage::getStoreConfig('trans_email/ident_general/name'),
                'email' => Mage::getStoreConfig('trans_email/ident_general/email')
            );
            $emailTemplate->sendTransactional($templateId, $sender, $sponsor->getEmail(), $sponsor->getName(), $vars);
        }

        // clean session
        $session->unsSponsorData();
    }
}
