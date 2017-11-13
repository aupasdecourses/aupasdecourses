<?php

class Apdc_Catalog_Block_Adminhtml_Catalog_Scripts_Details_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_sqlarray;

    public function __construct()
   {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('scripts_details_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->_sqlarray = Mage::helper('apdc_catalog/adminhtml_scripts')->getSqlRequests();
    }

    protected function _prepareCollection()
    {
      $select = Mage::getModel('apdc_catalog/adminhtml_request')->mySqlRequest($this->getData('sql'));
      $this->setCollection($select);
      return parent::_prepareCollection();
    }

    protected function _prepareColumnsSimple()
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
                    'header' => 'Name',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'name',
               ));
    }

    protected function _prepareColumnsBundle()
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

       $this->addColumn('nbe produits total',
             array(
                    'header' => 'nbe produits total',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'nbe produits total',
               ));

       $this->addColumn('nbe produits desactives',
             array(
                    'header' => 'nbe produits desactives',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'nbe produits desactives',
               ));

       $this->addColumn('percent',
             array(
                    'header' => 'percent',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'percent',
               ));

    }

    protected function _prepareColumnsCategories()
    {

       $this->addColumn('entity_id',
             array(
                    'header' => 'Id Categorie',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'category_id',
               ));

       $this->addColumn('nb_produits',
             array(
                    'header' => 'Nb produits',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'nb_produits',
               ));

    }

    protected function _prepareColumns()
    {
       if($this->_sqlarray[$this->getData('sql')]['grid']=='simple'){
          $this->_prepareColumnsSimple();
       }elseif($this->getData('sql')=='bundles'){
          $this->_prepareColumnsBundle();
       }elseif($this->getData('sql')=='cats_n_products'){
          $this->_prepareColumnsCategories();
       }

         return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        if($this->getData('sql')!='cats_n_products'){
          return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getId()));
        }else{
          return $this->getUrl('adminhtml/catalog_category/edit', array('id' => $row->getData('category_id')));
        }
    }

}