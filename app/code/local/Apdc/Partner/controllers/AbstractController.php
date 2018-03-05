<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_AbstractController 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Controller_Front_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
abstract class Apdc_Partner_AbstractController extends Mage_Core_Controller_Front_Action
{
    public function mainAction()
    {
        try {
            $authenticate = Mage::getModel('apdc_partner/authentication');
            if (!$authenticate->validateRequest($this->getRequest())) {
                throw new Exception('Invalid request');
            }
            $post = $this->getRequest()->getPost();
            $partner = Mage::getModel('apdc_partner/partner');
            if ($partner->login($post['key'], $this->getSignature())) {
                $this->execute($partner);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            die($e->getMessage());
            return $this->norouteAction();
        }
    }

    abstract protected function execute(Apdc_Partner_Model_Partner $partner);

    /**
     * getSignature
     * 
     * @return string
     */
    protected function getSignature()
    {
        $authorization = explode(' ', $this->getRequest()->getHeader('Authorization'));
        if (isset($authorization[1])) {
            return $authorization[1];
        }
        return '';
    }
}
