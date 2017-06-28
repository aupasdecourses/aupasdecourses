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
        }

        if (!$this->_isAllowedAction('delete')) {
            $this->_removeButton('delete');
        }
        if ($this->_isAllowedAction('update_categories')) {
            $this->_addButton('update_categories', array(
                'label'     => Mage::helper('adminhtml')->__('Enregistrer et mettre à jour les catégories'),
                'onclick'   => 'editForm.submit();',
                'class'     => 'save',
            ), 1);
        }
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

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/shop/' . $action);
    }
}
