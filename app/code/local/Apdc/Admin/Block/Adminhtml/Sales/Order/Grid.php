<?php
/*See http://www.boolfly.com/admin-grid-magento/
/*https://magento2.atlassian.net/wiki/display/m1wiki/How+to+Override+the+Sales+Order+Search+grid
/*/

//In this case you have to override Block_Sales_Order_Grid and not Block_Widget_Grid however calling to return the grandparent Block_Widget_Grid prepareCollection in the current prepareCollection ...

class Apdc_Admin_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
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

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class.
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());

        // //Amasty Order Attributes & Attachments are integrated via observer

        //MWDdate
        $ddate = Mage::getModel('ddate/dtime')->getCollection();
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()
        ->joinLeft(array(
            'ddate_store' => $ddate->getTable('ddate_store'),
        ), 'ddate_store.increment_id = main_table.increment_id', array(
            'ddate_store.ddate_id',
        ))->joinLeft(array(
            'mwddate' => $ddate->getTable('ddate'),
        ), 'mwddate.ddate_id = ddate_store.ddate_id', array(
            'mwddate.ddate',
            'mwddate.dtimetext',
        ))->joinLeft(array(
            'mwdtime' => $ddate->getTable('dtime'),
        ), 'mwdtime.dtime_id = mwddate.dtime', array(
            'mwdtime.dtime',
            'mwdtime.dtime_id',
        ));

        //Address
         $collection->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id AND sales_flat_order_address.address_type = "shipping"', array('postcode', 'street'));
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
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
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
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'filter_index' => 'main_table.status',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        // $this->addColumn('shipping_name', array(
        //     'header' => Mage::helper('sales')->__('Ship to Name'),
        //     'index' => 'shipping_name'
        // ));

        $this->addColumn('street', array(
            'header' => Mage::helper('sales')->__('Adresse liv.'),
            'index' => 'street',
            'filter_index' => 'sales_flat_order_address.street',
        ));

        $this->addColumn('postcode', array(
            'header' => Mage::helper('sales')->__('Arr.'),
            'index' => 'postcode',
            'filter_index' => 'sales_flat_order_address.postcode',
        ));

        $this->addColumn('ddate', array(
            'header' => Mage::helper('ddate')->__('Date Liv.'),
            'index' => 'ddate',
            'type' => 'date',
            'width' => '60px',
            'format' => Mage::helper('ddate')->php_date_format_M('-'),
            'filter_condition_callback' => array(
                $this,
                '_myDdateFilter',
            ),
        ));

        $this->addColumn('dtimetext', array(
            'header' => Mage::helper('ddate')->__('Heure liv.'),
            'align' => 'center',
            'width' => '60px',
            'index' => 'dtimetext',
            'type' => 'text',
            'filter_index' => 'mwddate.dtimetext',
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
            'currency' => 'order_currency_code',
        ));

        // //Amasty Order Attributes Attachments, Flags ... are added via observer, use the following functions to reorder the produit_equivalent column, via http://sofc.developer-works.com/article/21770138/Reposition+Magento%26%2339%3Bs+admin+grid+column+at+first+position

        $this->addColumnsOrder('produit_equivalent', 'grand_total');
        $this->sortColumnsByOrder();

        // Remove columns
        $this->removeColumn('base_grand_total');

        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        //return parent::_prepareColumns();
    }

    protected function _myDdateFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $from = Mage::getSingleton('core/date')->gmtDate('Y-m-d', $value['orig_from']);
        $to = Mage::getSingleton('core/date')->gmtDate('Y-m-d', $value['orig_to']);
        $this->getCollection()->getSelect()->where("mwddate.ddate >= '".$from."' AND ddate <= '".$to."'");

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label' => Mage::helper('sales')->__('Cancel'),
                 'url' => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label' => Mage::helper('sales')->__('Hold'),
                 'url' => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label' => Mage::helper('sales')->__('Unhold'),
                 'url' => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label' => Mage::helper('sales')->__('Print Invoices'),
             'url' => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label' => Mage::helper('sales')->__('Print Packingslips'),
             'url' => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label' => Mage::helper('sales')->__('Print Credit Memos'),
             'url' => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label' => Mage::helper('sales')->__('Print All'),
             'url' => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label' => Mage::helper('sales')->__('Print Shipping Labels'),
             'url' => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }

        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
