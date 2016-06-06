<?php

class MW_Ddate_IndexController extends Mage_Core_Controller_Front_Action
{
    public function testAction(){
        print_r(date("d F Y H:i:s", Mage::getSingleton('core/date')->timestamp()));
    }
}