<?php

class Apdc_Neighborhood_Model_Observer
{

    /**
     * setNeighborhoodVisiting
     * set neighborhood in customer session
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function setNeighborhoodVisiting(Varien_Event_Observer $observer)
    {
        $websiteId = Mage::app()->getWebsite()->getId();
        $neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection()
            ->addFieldToFilter('website_id', $websiteId);

        if ($neighborhoods->count() > 0) {
            Mage::getSingleton('customer/session')
                ->setNeighborhoodVisiting($neighborhoods->getFirstItem());
        } else {
            Mage::getSingleton('customer/session')->setNeighborhoodVisiting(null);
        }
    }
}
