<?php


require_once(Mage::getModuleDir('controllers','Apdc_Partner').DS.'AbstractController.php');

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
 * Apdc_Partner_SalesController 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Apdc
 * @uses     Apdc_Partner_AbstractController
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_SalesController extends Apdc_Partner_AbstractController
{
    /**
     * listAction 
     * 
     * @return string
     */
    public function listAction()
    {
        return parent::mainAction();
    }

    protected function execute(Apdc_Partner_Model_Partner $partner)
    {
        try {
            $post = $this->getRequest()->getPost();
            $sales = Mage::getModel('apdc_partner/data_sales')
                ->setPartner($partner)
                ->setSalesData($post);

            echo $sales->getList();
        } catch (Exception $e) {
            echo json_encode(['message' => $e->getMessage(), 'error' => 500]);
            exit(1);
        }

        return;
    }
}
