<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Commercant_Grid
 */
class Apdc_Commercant_Block_Adminhtml_Commercant_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('commercantGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /** @var Apdc_Commercant_Model_Resource_Commercant_Collection $collection */
        $collection = Mage::getModel('apdc_commercant/commercant')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header' => $this->__('ID'),
            'index' => 'id_commercant',
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
        return $this->getUrl('*/*/edit', ['id_commercant' => $row->getId()]);
    }
}
