<?php

class Apdc_Customer_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('customer_neighborhood', 'customer/customer_neighborhood', 'entity_id', null, 'left')
            ->joinAttribute('inchoo_socialconnect_fid', 'customer/inchoo_socialconnect_fid', 'entity_id', null, 'left')
            ->joinAttribute('inchoo_socialconnect_gid', 'customer/inchoo_socialconnect_gid', 'entity_id', null, 'left')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $collection->getSelect()->join(
            ['neighborhood' => $collection->getTable('apdc_neighborhood')],
            'neighborhood.entity_id = at_customer_neighborhood.value',
            [
                'neighborhood_name' => 'neighborhood.name',
                'neighborhood_id' => 'neighborhood.entity_id'
            ]
        );

        $collection->getSelect()->columns([
            'facebook' => 'IF (at_inchoo_socialconnect_fid.value IS NOT NULL, 2, 1)',
            'google' => 'IF (at_inchoo_socialconnect_gid.value IS NOT NULL, 2, 1)'
        ]);

        $this->setCollection($collection);

        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn(
            'neighborhood_name',
            [
                'header' => Mage::helper('customer')->__('Quartier'),
                'align' => 'center',
                'type' => 'options',
                'options' => Mage::getModel('apdc_neighborhood/source_option_neighborhood')->getOptionArray(false),
                'filter_condition_callback' => array($this, 'customFilterCallback'),
                'filter_index' => 'neighborhood.entity_id',
                'index' => 'neighborhood_name'
            ]
        );
        $this->addColumn(
            'facebook',
            [
                'header' => Mage::helper('customer')->__('Facebook account linked'),
                'align' => 'center',
                'type' => 'options',
                'options' => array(1 => Mage::helper('customer')->__('No'), 2 => Mage::helper('customer')->__('Yes')),
                'filter_index' => 'IF (at_inchoo_socialconnect_fid.value IS NOT NULL, 2, 1)',
                'filter_condition_callback' => array($this, 'customFilterCallback'),
                'index' => 'facebook'
            ]
        );
        $this->addColumn(
            'google',
            [
                'header' => Mage::helper('customer')->__('Google account linked'),
                'align' => 'center',
                'type' => 'options',
                'options' => array(1 => Mage::helper('customer')->__('No'), 2 => Mage::helper('customer')->__('Yes')),
                'filter_index' => 'IF (at_inchoo_socialconnect_gid.value IS NOT NULL, 2, 1)',
                'filter_condition_callback' => array($this, 'customFilterCallback'),
                'index' => 'google'
            ]
        );
        $this->sortColumnsByOrder();
        return $this;
    }

    /**
     * customFilterCallback
     * 
     * @param Mage_Customer_Model_Resource_Customer_Collection $collection : collection 
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column     : column 
     * 
     * @return Mage_Adminhtml_Block_Customer_Grid
     */
    protected function customFilterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value > 0) {
            $collection->getSelect()->where($column->getFilterIndex() . ' = ?', $value);
        }
        return $this;
    }
}
