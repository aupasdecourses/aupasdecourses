<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Shop_Grid
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('shopGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /** @var Apdc_Commercant_Model_Resource_Shop_Collection $collection */
        $collection = Mage::getModel('apdc_commercant/shop')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header' => $this->__('ID'),
            'index' => 'id_shop',
        ]);

        $this->addColumn('name', [
            'header' => $this->__('Name'),
            'index' => 'name',
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
