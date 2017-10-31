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
        $mainCategoryLabel = Mage::getSingleton('apdc_commercant/source_shop_mainCategoryLabel')->toOptionArray();
        $fieldset->addField(
            'main_category_label',
            'select',
            [
                'name' => 'main_category_label',
                'label' => $this->__('Libellé de la catégorie principale'),
                'required' => true,
                'values' => $mainCategoryLabel
            ]
        );

        $fieldset->addType('image_browser', 'Apdc_Media_Model_Data_Form_Element_ImageBrowser');

        $fieldset->addField(
            'category_image',
            'image_browser',
            [
                'name' => 'category_image',
                'label' => $this->__('Image'),
                'required' => false,
                'style' => 'height:22px; width50%;',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'category_thumbnail',
            'image_browser',
            [
                'name' => 'category_thumbnail',
                'label' => $this->__('Thumbnail'),
                'required' => false,
                'style' => 'height:22px; width50%;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'category_meta_title',
            'text',
            [
                'name' => 'category_meta_title',
                'label' => $this->__('Meta Title'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'category_meta_description',
            'textarea',
            [
                'name' => 'category_meta_description',
                'label' => $this->__('Meta Description'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'category_description',
            'textarea',
            [
                'name' => 'category_description',
                'label' => $this->__('Description'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );


        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
        } else {
            if (!$model->getCategoryMetaTitle()) {
                $model = $this->setDefaultCategoryValues($model);
            }
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * setDefaultCategoryValues 
     * 
     * @param Apdc_Commercant_Model_Shop $model model 
     * 
     * @return Apdc_Commercant_Model_Shop
     */
    protected function setDefaultCategoryValues(Apdc_Commercant_Model_Shop $model)
    {
        $categoryIds = $model->getCategoryIds();
        if (!empty($categoryIds)) {
            $category = Mage::getModel('catalog/category')->load($categoryIds[0]);
            if ($category && $category->getId()) {
                $thumbnailValue = $category->getThumbnail();
                $imageValue = $category->getImage();
                if ($thumbnailValue && !preg_match('/^wysiwyg\//', $thumbnailValue) && !preg_match('/^catalog\/category\//', $thumbnailValue)) {
                    $thumbnailValue = 'catalog/category/' . $thumbnailValue;
                }
                if ($imageValue && !preg_match('/^wysiwyg\//', $imageValue) && !preg_match('/^catalog\/category\//', $imageValue)) {
                    $imageValue = 'catalog/category/' . $imageValue;
                }

                $model->setCategoryImage($imageValue)
                    ->setCategoryThumbnail($thumbnailValue)
                    ->setCategoryMetaTitle($category->getMetaTitle())
                    ->setCategoryMetaDescription($category->getMetaDescription())
                    ->setCategoryDescription($category->getDescription());

                if (!$model->getCategoryMetaTitle()) {
                    $model->setCategoryMetaTitle($model->getName());
                }
            }
        }

        return $model;
    }

    /**
     * getTableLabel
     * 
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__('Informations de Catégories');
    }

    /**
     * getTabTitle
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('Informations de Catégories');
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
