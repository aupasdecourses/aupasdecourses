<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_Google 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_Categories
    extends Mage_Adminhtml_Block_Widget_Form 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('shop');
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'categories',
            ['legend' => $this->__('Informations sur les catégories')]
        );

        $commercantCategories = Mage::getModel('catalog/category')
            ->getCollection()
            ->setOrder('name')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('estcom_commercant', 70);
        $values = [];
        $S = Mage::helper('apdc_commercant')->getStoresArray();
        foreach ($commercantCategories as $category) {
            $storename=$S[explode('/', $category->getPath())[1]]['name'];
            $parentcat=$category->getParentCategory()->getName();
            $values[]=['value'=>$category->getId(), 'label' => $category->getName().' - '.$parentcat.' - '.$storename];
        }

        $fieldset->addField('id_category', 'multiselect', [
            'name' => 'id_category',
            'label' => $this->__('Categorie(s)'),
            'required' => true,
            'values' => $values,
            'note' => $this->__('Catégorie(s) correspondante(s) aux produits du magasin'),
            'disabled' => $isElementDisabled
        ]);


        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
        } else {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * getTableLabel
     * 
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__('Catégories');
    }

    /**
     * getTabTitle
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('Catégories');
    }

    /**
     * canShowTab
     * 
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * isHidden
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * _helper 
     * 
     * @return Apdc_Neighborhood_Helper_Data
     */
    private function _helper()
    {
        return Mage::helper('apdc_commercant');
    }

    /**
     * _isAllowedAction
     * 
     * @param string $action : action
     * 
     * @return boolean
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/shop/' . $action);
    }
}
