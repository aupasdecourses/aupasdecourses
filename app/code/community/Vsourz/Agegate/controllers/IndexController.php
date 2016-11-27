<?php

class Vsourz_Agegate_IndexController extends Mage_Core_Controller_Front_Action
{
    public function setagecookieAction()
    {
        $params = $this->getRequest()->getParams();
        try {
            Mage::getModel('core/cookie')->set($params['name'], $params['statut']);
        } catch (Exception $e) {
        }
    }
}
