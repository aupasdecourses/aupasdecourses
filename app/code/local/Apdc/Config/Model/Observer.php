<?php
/**
 * @author Pierre Mainguet
 * @copyright Copyright (c) 2016 Pierre Mainguet - mainguetpierre@gmail.com
 */
class Apdc_Config_Model_Observer
{
    public function is_commercant($id)
    {
        return Mage::getResourceModel('catalog/category')->getAttributeRawValue($id, 'estcom_commercant', Mage::app()->getStore()->getId());
    }

    public function has_children($id)
    {
        $string = Mage::getModel('catalog/category')->load($id)->getChildren();
        if ($string == '') {
            return false;
        } else {
            return true;
        }
    }

    /*Add custom handles for Commerçant page only*/

    public function addCustomhandle(Varien_Event_Observer $observer)
    {
        $type = Mage::app()->getFrontController()->getRequest()->getControllerName();

        if ($type == 'category') {
            $layout = $observer->getEvent()->getLayout();
            $id = Mage::registry('current_category')->getId();
            $currentcat = Mage::getSingleton('catalog/category')->load($id);

                //check if category is Page commerçant
                $estcom = $this->is_commercant($id);

                //check if category has children
                $haschildren = $this->has_children($id);

                //check if parent category is page commerçant
                $parentiscom = $this->is_commercant($currentcat->getParentCategory()->getId());

            if ($estcom) {
                $layout->getUpdate()->addHandle('PAGE_COMMERCANT');
            } elseif (!$estcom && $parentiscom && $haschildren) {
                $layout->getUpdate()->addHandle('GROUP_CATEGORY');
            }
        }
    }
}
