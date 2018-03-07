<?php

require_once '../abstract.php';

class Pmainguet_CleanCatInfo extends Mage_Shell_Abstract
{

    public function shellfixCats($store = null)
    {
        $cats = Mage::helper('apdc_referentiel')->getCats(1,6,$store);
        foreach ($cats as $cat) {            
            Mage::getModel('apdc_referentiel/cleancat')->process($cat);
        }
        Mage::getModel('apdc_referentiel/cleancat')::setcorrectchildrennumber();
        Mage::getModel('apdc_referentiel/cleancat')::clearcache();
    }

    // Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $store = $this->getArg('store');
        if (isset($store)) {
            $this->shellfixCats($store);
        } else {
            $this->shellfixCats();
        }
    }
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CleanCatInfo();
$shell->run();
