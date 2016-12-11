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
}
