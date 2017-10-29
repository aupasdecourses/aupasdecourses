<?php

class Apdc_Catalog_Block_Adminhtml_Catalog_Scripts_Details_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
   {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('scripts_details_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
      $select = Mage::getModel('apdc_catalog/adminhtml_request')->mySqlRequest($this->getData('sql'));
      $this->setCollection($select);
      return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
       $this->addColumn('entity_id',
             array(
                    'header' => 'Entity Id',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'entity_id',
               ));

       $this->addColumn('sku',
             array(
                    'header' => 'SKU',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'sku',
               ));

       $this->addColumn('name',
               array(
                    'header' => 'name',
                    'align' =>'left',
                    'index' => 'name',
              ));

         return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getId()));
    }

}
