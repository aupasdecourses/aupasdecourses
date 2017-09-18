<?php

/**
 * Class Apdc_Commercant_Adminhtml_Commercant_ContactController
 */
class Apdc_Commercant_Adminhtml_Commercant_ContactController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/contact'); 
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('neighborhoods/contact')
            ->_addBreadcrumb($this->__('Contact'), $this->__('Contact'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Contact'))->_title($this->__('Listing des contacts'))
            ->_initAction()
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_contact'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Contact'));
        $id = $this->getRequest()->getParam('id_contact');
        $entity = Mage::getModel('apdc_commercant/contact');

        if ($id) {
            $entity->load($id);
            if (!$entity->getId()) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('This entity does not exist'));
            }
        }

        $this->_title($id ? $this->__('Editer contact') : $this->__('Nouveau contact'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $entity->setData($data);
        }
        Mage::register('contact', $entity);

        $crumb = $id ? $this->__('Editer contact') : $this->__('Nouveau contact');

        $this
            ->_initAction()
            ->_addBreadcrumb($crumb, $crumb)
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_contact_edit'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if (!($data = $this->getRequest()->getPost())) {
            return $this->_redirect('*/*/');
        }
        $id = $this->getRequest()->getParam('id_contact');
        $model = Mage::getModel('apdc_commercant/contact')->load($id);
        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This entity does not exist'));
            return $this->_redirect('*/*/');
        }

        $data = $this->_filterDates($data, ['dob']);
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
        $id = $this->getRequest()->getParam('id_contact');
        $entity = Mage::getModel('apdc_commercant/contact');

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
