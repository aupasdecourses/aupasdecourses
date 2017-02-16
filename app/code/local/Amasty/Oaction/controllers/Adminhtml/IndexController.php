<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */
class Amasty_Oaction_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    const MAX_LINE   = 2000;
    const BATCH_SIZE = 1000;
    const FIELDS     = 4;

    protected function _construct()
    {
        $this->setUsedModuleName('Amasty_Oaction');
    }
    
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('system/amoaction');
        return $this;
    }
    
    public function indexAction()
    {
        $this->editAction();
    }
    
    public function newAction() 
    {
        $this->editAction();
    }
    
    public function saveAction()
    {
        $helper = Mage::helper('amoaction');
        try {
            if (empty($_FILES['csv_file']['name'])){
                throw new Exception('No file');
            }
            $fileName = $_FILES['csv_file']['tmp_name'];
            
            //for Mac OS
            ini_set('auto_detect_line_endings', 1);
            
            //file can be very big, so we read it by small chunks
            $fp = fopen($fileName, 'r');
            if (!$fp) {
                throw new Exception($helper->__('Can not open file'));   
            }
            
            $currRow = 0;
            while (($line = fgetcsv($fp, self::MAX_LINE, ',', '"')) !== false) {
                $currRow++;
                
                $checkCount = self::FIELDS - count($line);
                if (!in_array($checkCount, array(0, 1))) {
                    $this->_getSession()->addError($helper->__('Error: Line #%d has been skipped: expected number of columns is %d', $currRow, self::FIELDS));
                    continue;
                } 
                
                // validate  data - not empty but title
                for ($i = 0; $i < self::FIELDS-1; $i++) {             
                    $line[$i] = trim($line[$i], "\r\n\t ".'"');
                    if (!$line[$i]) {
                        $this->_getSession()->addError($helper->__('Error: Line #%d has been skipped: contains empty columns', $currRow));
                        continue;
                    }
                }
                
                $order = Mage::getModel('sales/order')->loadByIncrementId($line[0]);
                $id = array(0 => $order->getId());
                Mage::app()->getRequest()->setPost('tracking', $order->getId() . '|' . $line[1]);
                Mage::app()->getRequest()->setPost('carrier', $order->getId() . '|' . $line[2]);
                if (!isset($line[3])) {
                    $line[3] = '';
                }
                Mage::app()->getRequest()->setPost('comment', $order->getId() . '|' . $line[3]);
                
                try {
                    $command = Amasty_Oaction_Model_Command_Abstract::factory('ship');
                    
                    $success = $command->execute($id, true);
                    
                    if ($success) {
                         $this->_getSession()->addSuccess($success);
                    }
                    
                    // show non critical errors to the user
                    foreach ($command->getErrors() as $err) {
                        $this->_getSession()->addError(
                            $this->__('Error: %s', $helper->__($err))
                        );
                    }            
                } catch (Exception $e) {
                    $error = $helper->__($e->getMessage());
                    $this->_getSession()->addError($this->__('Error: %s', $error));
                }
            }
            fclose($fp);
        } catch (Exception $e) {
            $error = $helper->__($e->getMessage());
            $this->_getSession()->addError($this->__('Error: %s', $error));
        }

        $this->_redirect('*/*/edit');
    }
    
    public function editAction()
    {
        $this->_initAction()
            ->_title(Mage::helper('amoaction')->__('Mass Order Actions'))
            ->_addContent($this->getLayout()->createBlock('amoaction/adminhtml_index_edit'))
            ->renderLayout();
    }
    
    public function doAction()
    {
        $ids         = $this->getRequest()->getParam('order_ids');
        $val         = trim($this->getRequest()->getParam('amoaction_value'));        
        $commandType = trim($this->getRequest()->getParam('command'));
		
		// pre-save url here as some action may change current store
        // in multi-store env it can result in admin url change		
	    $url =        $this->getUrl('adminhtml/sales_order');
        
        try {
            $command = Amasty_Oaction_Model_Command_Abstract::factory($commandType);
            
            $success = $command->execute($ids, $val);
            if ($success) {
                 $this->_getSession()->addSuccess($success);
            }
            
            // show non critical errors to the user
            foreach ($command->getErrors() as $err) {
                 $this->_getSession()->addError($err);
            }            
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Error: %s', $e->getMessage()));
        } 
        
        if ($command->hasResponse()) {
            $this->_prepareDownloadResponse(
                $command->getResponseName(), 
                $command->getResponseBody(),
                $command->getResponsetype()
            );            
        } 
        else {
			$this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
			$this->getResponse()->setRedirect($url);
        }
		
        return $this;        
    }
}