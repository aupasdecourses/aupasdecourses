<?php

/**
 * Class Apdc_Commercant_Adminhtml_Commercant_CommercantController
 */
class Apdc_Commercant_Adminhtml_Commercant_CommercantController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/commercant')
            ->_addBreadcrumb($this->__('Commercant'), $this->__('Commercant'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Commercant'))->_title($this->__('Listing des commercants'))
            ->_initAction()
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_commercant'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Commercant'));
        $id = $this->getRequest()->getParam('id_commercant');
        $entity = Mage::getModel('apdc_commercant/commercant');

        if ($id) {
            $entity->load($id);
            if (!$entity->getId()) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('This entity does not exist'));
            }
        }

        $this->_title($id ? $this->__('Editer commercant') : $this->__('Nouveau commercant'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $entity->setData($data);
        }
        Mage::register('commercant', $entity);

        $crumb = $id ? $this->__('Editer commercant') : $this->__('Nouveau commercant');

        $this
            ->_initAction()
            ->_addBreadcrumb($crumb, $crumb)
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_commercant_edit'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if (!($data = $this->getRequest()->getPost())) {
            return $this->_redirect('*/*/');
        }
        $id = $this->getRequest()->getParam('id_commercant');
        $model = Mage::getModel('apdc_commercant/commercant')->load($id);
        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This entity does not exist'));
            return $this->_redirect('*/*/');
        }

        $model->setData($data);
        $model->save();
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The entity has been saved.'));
        Mage::getSingleton('adminhtml/session')->setFormData(false);
        if ($this->getRequest()->getParam('back')) {
            return $this->_redirect('*/*/edit', ['id' => $id]);
        }
        return $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id_commercant');
        $entity = Mage::getModel('apdc_commercant/commercant');

        if (!$id) {
            $this->_redirect('*/*');
            return;
        }

        $entity->load($id);
        if (!$entity->getId()) {
            Mage::getSingleton('adminhtml/session')
                ->addError($this->__('This entity does not exist'));
            $this->_redirect('*/*');
            return;
        }

        try {
            $entity->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The entity has been deleted.'));
            $this->_redirect('*/*');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('The entity cannot be deleted. Please check it is not associated with any other object.');
            $this->_redirect('*/*');
        }
    }
}
