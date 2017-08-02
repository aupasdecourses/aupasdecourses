<?php

/**
 * Class Apdc_Commercant_Adminhtml_Commercant_ShopController
 */
class Apdc_Commercant_Adminhtml_Commercant_ShopController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/shop'); 
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
        $entity = $this->_initShop();
        $shopId = $this->getRequest()->getParam('id_shop');

        if ($shopId && $entity->getId() != $shopId) {
            Mage::getSingleton('adminhtml/session')
                ->addError($this->__('This entity does not exist'));
            return $this->_redirect('*/*/index');
        }

        $this->_title($entity->getId() ? $this->__('Editer magasin') : $this->__('Nouveau magasin'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $entity->setData($data);
        }

        $crumb = $entity->getId() ? $this->__('Editer magasin') : $this->__('Nouveau magasin');

        $this
            ->_initAction()
            ->_addBreadcrumb($crumb, $crumb)
            ->renderLayout();
    }

    public function saveAction()
    {
        if (!($data = $this->getRequest()->getPost())) {
            return $this->_redirect('*/*/');
        }
        $id = $this->getRequest()->getParam('id_shop');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $updateCategories = (bool)$this->getRequest()->getParam('update_categories', false);

        try {
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

            if (isset($data['id_category'])) {
                if ($data['id_category'] != -1) {
                    $data['id_category'] = explode(',', $data['id_category']);
                    $data['id_category'] = array_unique($data['id_category']);
                }

                $data["stores"] = [];
                $S = Mage::helper('apdc_commercant')->getStoresArray();
                $categories=Mage::getModel ('catalog/category');
                foreach ($data["id_category"] as $id) {
                    $cat=$categories->getCollection()->addAttributeToSelect("path")
                        ->addAttributetoFilter ("entity_id",$id)->getFirstItem ();
                    $storeid=$S[explode('/', $cat->getPath())[1]]['store_id'];
                    if(!in_array($storeid,$data["stores"])){
                        $data["stores"][]=$storeid;
                    }
                }
            }


            $model->setData($data);
            $model->save();
            if ($updateCategories) {
                $this->updateCategories($model);
            }
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The entity has been saved.'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Erreur : impossible d\'enregistrer le magasin. Veuillez consulter les logs pour en savoir plus.'));
            Mage::logException($e);
            $redirectBack = true;
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $id,
                '_current'=>true
            ));
        } else {
            return $this->_redirect('*/*/');
        }
    }

    /**
     * updateCategories 
     * 
     * @param Apdc_Commercant_Model_Shop $model model 
     * 
     * @return void
     */
    protected function updateCategories($model)
    {
        $categoryIds = ($model->getIdCategory() ? $model->getIdCategory() : array());
        if ($categoryIds && !is_array($categoryIds)) {
            $categoryIds = explode(',', $categoryIds);
        }
        if (!empty($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                if ($category && $category->getId()) {
                    $category->setImage($model->getCategoryImage())
                        ->setThumbnail($model->getCategoryThumbnail())
                        ->setMetaTitle($model->getCategoryMetaTitle())
                        ->setMetaDescription($model->getCategoryMetaDescription())
                        ->setDescription($model->getCategoryDescription())
                        ->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('La catégorie <strong>%s</strong> a été mise à jour avec succès.', $category->getName()));
                }
            }
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id_shop');

        if (!$id) {
            $this->_redirect('*/*');
            return;
        }

        $entity = $this->_initShop();
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
     * categoriesAction 
     * 
     * @return void
     */
    public function categoriesAction()
    {
        $this->_initShop();
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * categoriesJsonAction 
     * 
     * @return void
     */
    public function categoriesJsonAction()
    {
        $this->_initShop();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('apdc_commercant/adminhtml_shop_edit_tab_categoriesTreeView')
            ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * _initShop 
     * 
     * @return void
     */
    protected function _initShop()
    {
        $shopId  = (int) $this->getRequest()->getParam('id_shop');
        $shop    = Mage::getModel('apdc_commercant/shop');

        if ($shopId) {
            $shop->load($shopId);
        }
        Mage::register('shop', $shop);
        return $shop;
    }
}
