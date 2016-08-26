<?php

include_once("Mage/Checkout/controllers/OnepageController.php");
class Pmainguet_Validateaddress_Checkout_OnepageController extends Mage_Checkout_OnepageController
{

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            Mage::dispatchEvent('shipping_validation_checkout', $data);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

}