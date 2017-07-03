<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Shop_Edit
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id_shop';
        $this->_blockGroup = 'apdc_commercant';
        $this->_controller = 'adminhtml_shop';
        $this->_headerText = $this->__('Magasins');

        parent::__construct();

        if (!$this->_isAllowedAction('save')) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        } else {
            $this->_addButton(
                'save_and_edit_button',
                [
                    'label' => Mage::helper('catalog')->__('Save and Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
                    'class' => 'save'
                ]
            );
        }

        if (!$this->_isAllowedAction('delete')) {
            $this->_removeButton('delete');
        }
        if ($this->_isAllowedAction('update_categories')) {
            $this->_addButton(
                'update_categories',
                [
                    'label'     => Mage::helper('adminhtml')->__('Enregistrer et mettre à jour les catégories'),
                    'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndUpdateCategoriesUrl().'\')',
                    'class'     => 'save',
                ]
            );
        }
    }

    /**
     * getSaveAndContinueUrl 
     * 
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'update_categories' => false,
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    /**
     * getSaveAndUpdateCategoriesUrl 
     * 
     * @return string
     */
    public function getSaveAndUpdateCategoriesUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'update_categories' => true,
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }

        return $this->getUrl('*/*/save');
    }

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('commercant_shop_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'apdc_commercant_tabsJsTabs';
            $tabsBlockPrefix   = 'apdc_commercant_tabs_';
        }

        $this->_formScripts[] = "
            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
        ";
        return parent::_prepareLayout();
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/shop/' . $action);
    }
}
