<?php

/**
 * Class Apdc_Commercant_Adminhtml_Commercant_BankinfoController
 */
class Apdc_Commercant_Adminhtml_Commercant_BankinfoController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/bank_information'); 
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('neighborhoods/commercant')
            ->_addBreadcrumb($this->__('Commercant'), $this->__('Commercant'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Commercant'))->_title($this->__('Listing des infos bancaires'))
            ->_initAction()
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_bankInfo'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Infos bancaires'));
        $id = $this->getRequest()->getParam('id_bank_information');
        $entity = Mage::getModel('apdc_commercant/bankInfo');

        if ($id) {
            $entity->load($id);
            if (!$entity->getId()) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('This entity does not exist'));
            }
        }

        $this->_title($id ? $this->__('Editer info') : $this->__('Nouvelle info'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $entity->setData($data);
        }
        Mage::register('bankInfo', $entity);

        $crumb = $id ? $this->__('Editer info') : $this->__('Nouvelle info');

        $this
            ->_initAction()
            ->_addBreadcrumb($crumb, $crumb)
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_bankInfo_edit'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if (!($data = $this->getRequest()->getPost())) {
            return $this->_redirect('*/*/');
        }
        $id = $this->getRequest()->getParam('id_bank_information');
        /** @var Apdc_Commercant_Model_BankInfo $model */
        $model = Mage::getModel('apdc_commercant/bankInfo')->load($id);
        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This entity does not exist'));
            return $this->_redirect('*/*/');
        }

        $model->setData($data);
        $this->_handleFiles($model);
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
        $id = $this->getRequest()->getParam('id_bank_information');
        $entity = Mage::getModel('apdc_commercant/bankInfo');

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

    /**
     * @param Apdc_Commercant_Model_BankInfo $model
     */
    protected function _handleFiles(Apdc_Commercant_Model_BankInfo $model)
    {
        if (empty($_FILES)) {
            return;
        }
        $data = $model->getData();

        foreach ($_FILES as $field => $fieldData) {
            if (!empty($fieldData['tmp_name'])) {
                $uploader = new Mage_Core_Model_File_Uploader($fieldData);
                $result = $uploader->save($model->getUploadDir($field));
                $data[$field] = $result['file'];
            } else if (!empty($data[$field]['delete'])) {
                $data[$field] = '';
            }
        }

        $model->setData($data);
    }
}
