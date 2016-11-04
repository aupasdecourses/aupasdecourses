<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Contact_Grid
 */
class Apdc_Commercant_Block_Adminhtml_Contact_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('contactGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /** @var Apdc_Commercant_Model_Resource_Contact_Collection $collection */
        $collection = Mage::getModel('apdc_commercant/contact')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header' => $this->__('ID'),
            'index' => 'id_contact',
        ]);

        $this->addColumn('lastname', [
            'header' => $this->__('Last name'),
            'index' => 'lastname',
        ]);

        $this->addColumn('firstname', [
            'header' => $this->__('First name'),
            'index' => 'firstname',
        ]);

        parent::_prepareColumns();
    }

    /**
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
