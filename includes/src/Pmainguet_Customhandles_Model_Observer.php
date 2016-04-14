<?php
class Pmainguet_Customhandles_Model_Observer
{
    /**
     * Converts attribute set name of current product to nice name ([a-z0-9_]+).
     * Adds layout handle PRODUCT_ATTRIBUTE_SET_<attribute_set_nicename> after
     * PRODUCT_TYPE_<product_type_id> handle
     *
     * Event: controller_action_layout_load_before
     *
     * @param Varien_Event_Observer $observer
     */

    /*Add custom handles for Commerçant page only*/
    
    public function addCustomhandle(Varien_Event_Observer $observer)
    {
            $type=Mage::app()->getFrontController()->getRequest()->getControllerName();

            if ($type=='category'){
                $layout = $observer->getEvent()->getLayout();
                $id = Mage::registry('current_category')->getId();
                $estcom=Mage::getResourceModel('catalog/category')->getAttributeRawValue($id, "estcom_commercant", Mage::app()->getStore()->getId());
                if($estcom){
                    $layout->getUpdate()->addHandle('PAGE_COMMERCANT');
                }
            }
    }
}