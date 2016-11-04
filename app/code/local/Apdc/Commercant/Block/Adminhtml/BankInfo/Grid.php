<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_BankInfo_Grid
 */
class Apdc_Commercant_Block_Adminhtml_BankInfo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('bankInfoGrid');
        $this->setDefaultSort('owner_name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /** @var Apdc_Commercant_Model_Resource_BankInfo_Collection $collection */
        $collection = Mage::getModel('apdc_commercant/bankInfo')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header' => $this->__('ID'),
            'index' => 'id_bank_information',
        ]);

        $this->addColumn('owner_name', [
            'header' => $this->__('Owner'),
            'index' => 'owner_name',
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
