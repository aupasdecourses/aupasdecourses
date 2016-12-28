<?php

/**
 * Class Apdc_Commercant_Adminhtml_Commercant_ShopController
 */
class Apdc_Commercant_Adminhtml_Commercant_ShopController extends Mage_Adminhtml_Controller_Action
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
        $this->_title($this->__('Commercant'))->_title($this->__('Listing des magasins'))
            ->_initAction()
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_shop'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Magasin'));
        $id = $this->getRequest()->getParam('id_shop');
        $entity = Mage::getModel('apdc_commercant/shop');

        if ($id) {
            $entity->load($id);
            if (!$entity->getId()) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('This entity does not exist'));
            }
        }

        $this->_title($id ? $this->__('Editer magasin') : $this->__('Nouveau magasin'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $entity->setData($data);
        }
        Mage::register('shop', $entity);

        $crumb = $id ? $this->__('Editer magasin') : $this->__('Nouveau magasin');

        $this
            ->_initAction()
            ->_addBreadcrumb($crumb, $crumb)
            ->_addContent($this->getLayout()->createBlock('apdc_commercant/adminhtml_shop_edit'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if (!($data = $this->getRequest()->getPost())) {
            return $this->_redirect('*/*/');
        }
        $id = $this->getRequest()->getParam('id_shop');
        $model = Mage::getModel('apdc_commercant/shop')->load($id);
        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This entity does not exist'));
            return $this->_redirect('*/*/');
        }

        if (isset($data['timetable'])) {
            $data['timetable'] = serialize($data['timetable']);
        }
        if (isset($data['closing_periods']) && is_array($data['closing_periods'])) {
            $closingData = [];
            $i = 0;
            foreach ($data['closing_periods']['start'] as $startDate) {
                if (!empty($startDate) && !empty($data['closing_periods']['end'][$i])) {
                    $closingData[] = ['start' => $startDate, 'end' => $data['closing_periods']['end'][$i]];
                }
                $i++;
            }
            $data['closing_periods'] = serialize($closingData);
        } else {
            $data['closing_periods'] = serialize([]);
        }

        if (!isset($data['delivery_days'])) {
            $data['delivery_days'] = [];
        }

        foreach (['id_contact_manager', 'id_contact_employee', 'id_contact_employee_bis'] as $key) {
            if (empty($data[$key])) {
                // explicitely set the value to NULL to avoid foreign key issues
                $data[$key] = null;
            }
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
        $id = $this->getRequest()->getParam('id_shop');
        $entity = Mage::getModel('apdc_commercant/shop');

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
