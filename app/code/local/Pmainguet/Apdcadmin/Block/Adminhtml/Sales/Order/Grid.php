<?php
/*See http://www.boolfly.com/admin-grid-magento/
/*https://magento2.atlassian.net/wiki/display/m1wiki/How+to+Override+the+Sales+Order+Search+Grid
/*/

//In this case you have to override Block_Sales_Order_Grid and not Block_Widget_Grid however calling to return the grandparent Block_Widget_Grid prepareCollection in the current prepareCollection ...

class Pmainguet_Apdcadmin_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    //Amasty Order Attributes
    protected function _getAttributes()
    {
        if (is_null($this->_attributes)) {
            $attributes = Mage::getModel('eav/entity_attribute')->getCollection();
            $attributes->addFieldToFilter('entity_type_id', Mage::getModel('eav/entity')->setType('order')->getTypeId());
            $attributes->addFieldToFilter('show_on_grid', 1);
            $this->_attributes = $attributes;
        }
        return $this->_attributes;
    }

    //Amasty Order Attachments   
    protected function _getAttachments()
    {
        if (is_null($this->_attachments)) {
            $attachments = Mage::getModel('amorderattach/field')->getCollection();
            $attachments->addFieldToFilter('show_on_grid', 1);
            $this->_attachments = $attachments;
        }

        return $this->_attachments;
    }   

    protected function _prepareCollection()
    {
        
        $collection = Mage::getResourceModel($this->_getCollectionClass());


        // //Amasty Order Attributes & Attachments are integrated via observer

        //MWDdate

        $ddate      = Mage::getModel('ddate/dtime')->getCollection();
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()
        ->joinLeft(array(
            'ddate_store' => $ddate->getTable('ddate_store')
        ), 'ddate_store.increment_id = main_table.increment_id', array(
            'ddate_store.ddate_id'
        ))->joinLeft(array(
            'mwddate' => $ddate->getTable('ddate')
        ), 'mwddate.ddate_id = ddate_store.ddate_id', array(
            'mwddate.ddate',
            'mwddate.dtimetext'
        ))->joinLeft(array(
            'mwdtime' => $ddate->getTable('dtime')
        ), 'mwdtime.dtime_id = mwddate.dtime', array(
            'mwdtime.dtime',
            'mwdtime.dtime_id'
        ));

        //Address
         $collection->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id AND sales_flat_order_address.address_type = "shipping"',array('postcode','street'));

        $this->setCollection($collection);

        //Important to not use parent::_prepareCollection() but Mage_Adminhtml_Block_Widget_Grid (grandparent function)
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()

    {

        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            //'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id'
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true
            ));
        }
        
        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            //'width' => '100px'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses()
        ));
        
        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name'
        ));
        
        // $this->addColumn('shipping_name', array(
        //     'header' => Mage::helper('sales')->__('Ship to Name'),
        //     'index' => 'shipping_name'
        // ));

        $this->addColumn('street', array(
            'header' => Mage::helper('sales')->__('Adresse liv.'),
            'index' => 'street',
        ));

        $this->addColumn('postcode', array(
            'header' => Mage::helper('sales')->__('Arr.'),
            'index' => 'postcode',
        ));

        $this->addColumn('ddate', array(
            'header' => Mage::helper('ddate')->__('Date Liv.'),
            'index' => 'ddate',
            'type' => 'date',
            'width' => '60px',
            'format' => Mage::helper('ddate')->php_date_format_M("-"),
            'filter_condition_callback' => array(
                $this,
                '_myDdateFilter'
            )
        ));

        $this->addColumn('dtimetext', array(
            'header' => Mage::helper('ddate')->__('Heure liv.'),
            'align' => 'center',
            'width' => '60px',
            'index' => 'dtimetext',
            'type' => 'text'
        ));
        
        // $this->addColumn('base_grand_total', array(
        //     'header' => Mage::helper('sales')->__('G.T. (Base)'),
        //     'index' => 'base_grand_total',
        //     'type' => 'currency',
        //     'currency' => 'base_currency_code'
        // ));
        
        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('Total'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code'
        ));

        // //Amasty Order Attributes Attachments, Flags ... are added via observer, use the following functions to reorder the produit_equivalent column, via http://sofc.developer-works.com/article/21770138/Reposition+Magento%26%2339%3Bs+admin+grid+column+at+first+position

        $this->addColumnsOrder('produit_equivalent','grand_total');
        $this->sortColumnsByOrder();

        // Remove columns
        $this->removeColumn('base_grand_total');

        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));
        
        //return parent::_prepareColumns();
    }
    
}
