<?php

class Apdc_Catalog_Block_Adminhtml_Catalog_Scripts_Details_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
   {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('manage_stores_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
}
