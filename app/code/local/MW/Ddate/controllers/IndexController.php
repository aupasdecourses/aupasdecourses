<?php

class MW_Ddate_IndexController extends Mage_Core_Controller_Front_Action
{
    public function testAction(){
        print_r($this->getCurrentTime());
    }
}