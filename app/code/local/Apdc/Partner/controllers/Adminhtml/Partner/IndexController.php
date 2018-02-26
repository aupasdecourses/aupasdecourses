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
 * Apdc_Partner_Adminhtml_Partner_IndexController 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Adminhtml_Partner_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * initActions 
     * 
     * @return void
     */
    protected function initActions()
    {
        $this->loadLayout()->_setActiveMenu('system/apdc_partner');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Gestion des partenaires'), Mage::helper('adminhtml')->__('Gestion des partenaires'));

        return $this;
    }
    /**
     * indexAction 
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Gestion des partenaires'));
        $this->initActions();

        $this->renderLayout();
    }

    /**
     * newAction 
     * 
     * @return void
     */
    public function newAction()
    {
        $model = Mage::getModel('apdc_partner/partner');

        $formData = $this->_getFormData();
        if ($formData) {
            $this->_setFormData($formData);
            $model->addData($formData);
        } else {
            /** @var $helper Mage_Oauth_Helper_Data */
            $helper = Mage::helper('oauth');
            $model->setKey($helper->generateConsumerKey());
            $model->setSecret($helper->generateConsumerSecret());
            $this->_setFormData($model->getData());
        }

        Mage::register('current_partner', $model);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit page action
     */
    public function editAction()
    {
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->_getSession()->addError(Mage::helper('oauth')->__('Invalid ID parameter.'));
            $this->_redirect('*/*/index');
            return;
        }

        $model = Mage::getModel('apdc_partner/partner');
        $model->load($id);

        if (!$model->getId()) {
            $this->_getSession()->addError(Mage::helper('oauth')->__('Entry with ID #%s not found.', $id));
            $this->_redirect('*/*/index');
            return;
        }

        $model->addData($this->_filter($this->getRequest()->getParams()));
        Mage::register('current_partner', $model);

        $this->initActions();
        $this->renderLayout();
    }

    /**
     * deleteAction 
     * 
     * @return void
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('apdc_partner/partner')->load($id);

                $oldImage = $model->getImage();
                if (!empty($oldImage)) {
                    Mage::helper('apdc_partner/media')->deleteMedia($oldImage);
                }

                $oldBanner = $model->getImageBanner();
                if (!empty($oldBanner)) {
                    Mage::helper('apdc_partner/media')->deleteMedia($oldBanner);
                }

                $model->delete();
                $this->_getSession()->addSuccess($this->__('Le partenaire a été supprimé'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('entity_id' => $id));
                return;
            }
        }
        $this->_getSession()->addError($this->__('Impossible de trouver le partenaire à supprimer'));
        $this->_redirect('*/*/');
        return;
    }

    /**
     * saveAction 
     * 
     * @return void
     */
    public function saveAction()
    {
        $id = $this->getRequest()->getParam('entity_id');
        if (!$this->_validateFormKey()) {
            if ($id) {
                $this->_redirect('*/*/edit', array('id' => $id));
            } else {
                $this->_redirect('*/*/new', array('id' => $id));
            }
            return;
        }

        $data = $this->_filter($this->getRequest()->getParams());


        $model = Mage::getModel('apdc_partner/partner');

        if ($id) {
            if (!(int) $id) {
                $this->_getSession()->addError(
                    $this->__('Invalid ID parameter.'));
                $this->_redirect('*/*/index');
                return;
            }
            $model->load($id);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    $this->__('Entry with ID #%s not found.', $id));
                $this->_redirect('*/*/index');
                return;
            }
        } else {
            $dataForm = $this->_getFormData();
            if ($dataForm) {
                $data['key']    = $dataForm['key'];
                $data['secret'] = $dataForm['secret'];
            } else {
                // If an admin was started create a new consumer and at this moment he has been edited an existing
                // consumer, we save the new consumer with a new key-secret pair
                /** @var $helper Mage_Oauth_Helper_Data */
                $helper = Mage::helper('oauth');

                $data['key']    = $helper->generateConsumerKey();
                $data['secret'] = $helper->generateConsumerSecret();
            }
        }

        try {
            $model->addData($data);
            $model->save();
            $this->_getSession()->addSuccess($this->__('The partner has been saved.'));
            $this->_setFormData(null);
        } catch (Mage_Core_Exception $e) {
            $this->_setFormData($data);
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
            $this->getRequest()->setParam('back', 'edit');
        } catch (Exception $e) {
            $this->_setFormData(null);
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An error occurred on saving partner data.'));
        }

        if ($this->getRequest()->getParam('back')) {
            if ($id || $model->getId()) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
            } else {
                $this->_redirect('*/*/new');
            }
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * initPartner 
     * 
     * @return this
     */
    protected function initPartner()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $model = Mage::getModel('apdc_partner/partner');
        if ($id) {
            $model->load($id);
        }
        Mage::register('current_partner', $model);
        return $this;
    }

    /**
     * Get form data
     *
     * @return array
     */
    protected function _getFormData()
    {
        return $this->_getSession()->getData('partner_data', true);
    }

    /**
     * Set form data
     *
     * @param $data
     * @return this
     */
    protected function _setFormData($data)
    {
        $this->_getSession()->setData('partner_data', $data);
        return $this;
    }

    /**
     * Unset unused data from request
     * Skip getting "key" and "secret" because its generated from server side only
     *
     * @param array $data
     * @return array
     */
    protected function _filter(array $data)
    {
        foreach (array('id', 'back', 'form_key', 'key', 'secret') as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        return $data;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();
        if ('index' == $action) {
            $action = null;
        } else {
            if ('new' == $action || 'save' == $action) {
                $action = 'edit';
            }
            $action = '/' . $action;
        }
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        return $session->isAllowed('system/apdc_partner' . $action);
    }
}
