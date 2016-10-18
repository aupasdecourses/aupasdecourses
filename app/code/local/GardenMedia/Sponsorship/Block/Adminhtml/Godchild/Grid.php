<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * GardenMedia_Sponsorship_Block_Adminhtml_Godchild_Grid 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Adminhtml_Godchild_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('godchildGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * _prepareCollection 
     * 
     * @return void
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addNameToSelect()
            ->addAttributeToSelect('email');
        $collection->setIsFromSponsorship(true);

        $collection->getSelect()->join(
            array('godchild' => $collection->getTable('gm_sponsorship/godchild')),
            'e.entity_id = godchild.godchild_id',
            array('*')
        );


        // If godchild's coupon is generated 
        $collection->getSelect()->joinLeft(
            array('rewards' => $collection->getTable('gm_sponsorship/salesrule_coupon_customer')),
            'rewards.customer_owner_id = godchild.godchild_id AND rewards.owner_type = "godchild"',
            array(
                'has_rewards' => 'IF(rewards.coupon_id IS NULL, 0, 1)'
            )
        );

        // If godchild's coupon is used
        $collection->getSelect()->joinLeft(
            array('coupon_used' => $collection->getTable('salesrule/coupon_usage')),
            'coupon_used.customer_id = godchild.godchild_id AND coupon_used.coupon_id = rewards.coupon_id AND coupon_used.times_used >= 1',
            array('is_used' => 'IF(coupon_used.coupon_id IS NULL, 0, 1)')
        );


        // Add sponsor informations (email)
        $collection->getSelect()->join(
            array('sponsor' => $collection->getTable('customer/entity')),
            'sponsor.entity_id = godchild.sponsor_id',
            array('sponsor_email' => 'sponsor.email')
        );

        // Add sponsor Name
        $collection->getSelect()->joinLeft(
            array('at_sponsor_firstname' => $collection->getTable('customer/entity') . '_varchar'),
            'at_sponsor_firstname.entity_id = sponsor.entity_id AND at_sponsor_firstname.attribute_id = 5',
            array()
        );
        $collection->getSelect()->joinLeft(
            array('at_sponsor_lastname' => $collection->getTable('customer/entity') . '_varchar'),
            'at_sponsor_lastname.entity_id = sponsor.entity_id AND at_sponsor_lastname.attribute_id = 7',
            array()
        );
        $collection->getSelect()->columns($this->getSponsorNameIndex() . ' AS sponsor_name');

        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter(
                'main_table.state',
                array(
                    'in' => array(
                        Mage_Sales_Model_Order::STATE_PROCESSING,
                        Mage_Sales_Model_Order::STATE_COMPLETE
                    )
                )
            )
            ->addFieldToSelect('customer_id');

        $orders->getSelect()->join(
            array('invoice' => $orders->getTable('sales/invoice')),
            'invoice.order_id = main_table.entity_id',
            array(
                'invoice_id' => 'entity_id',
                'order_id' => 'order_id'
            )
        );
        $orders->getSelect()->columns('IFNULL(SUM(main_table.base_total_invoiced), 0) - IFNULL(SUM(main_table.base_total_refunded), 0) as total_amount');
        $orders->getSelect()->group('customer_id');

        $collection->getSelect()->joinLeft(
            array('orders' => new Zend_Db_Expr('(' . $orders->getSelect() . ')')),
            'orders.customer_id = e.entity_id',
            array('total_amount')
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * _prepareColumns 
     * 
     * @return void
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'godchild_id',
            array(
                'header' => $this->_helper()->__('Godchild Id'),
                'type' => 'number',
                'align' => 'left',
                'index' => 'godchild_id'
            )
        );

        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('customer')->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn(
            'email',
            array(
                'header' => Mage::helper('customer')->__('Email'),
                'width' => '150',
                'index' => 'email'
            )
        );

        $this->addColumn(
            'total_amount',
            array(
                'header' => Mage::helper('customer')->__('Total Ordered'),
                'type' => 'price',
                'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
                'index' => 'total_amount',
                'filter_index' => 'total_amount',
                'filter_condition_callback' => array($this, 'filterById'),
            )
        );

        $this->addColumn(
            'has_rewards',
            array(
                'header' => $this->_helper()->__('Coupon Generated'),
                'type' => 'options',
                'options' => array(
                    0 => $this->_helper()->__('No'),
                    1 => $this->_helper()->__('Yes')
                ),
                'filter_index' =>'IF(rewards.coupon_id IS NULL, 0, 1)',
                'filter_condition_callback' => array($this, 'filterForRewards'),
                'index' => 'has_rewards'
            )
        );
        $this->addColumn(
            'is_used',
            array(
                'header' => $this->_helper()->__('Coupon Used'),
                'type' => 'options',
                'options' => array(
                    0 => $this->_helper()->__('No'),
                    1 => $this->_helper()->__('Yes')
                ),
                'filter_index' => 'IF(coupon_used.coupon_id IS NULL, 0, 1)',
                'filter_condition_callback' => array($this, 'filterForRewards'),
                'index' => 'is_used'
            )
        );


        $this->addColumn(
            'sponsor_id',
            array(
                'header' => $this->_helper()->__('Sponsor Id'),
                'type' => 'number',
                'align' => 'left',
                'filter_index' => 'godchild.sponsor_id',
                'filter_condition_callback' => array($this, 'filterById'),
                'index' => 'sponsor_id'
            )
        );

        $this->addColumn(
            'sponsor_email',
            array(
                'header' => Mage::helper('customer')->__('Sponsor Email'),
                'width' => '150',
                'index' => 'sponsor_email',
                'filter_index' => 'sponsor.email',
                'filter_condition_callback' => array($this, 'filterSponsor')
            )
        );

        $this->addColumn(
            'sponsor_name',
            array(
                'header' => Mage::helper('customer')->__('Sponsor Name'),
                'index' => 'sponsor_name',
                'filter_index' => $this->getSponsorNameIndex(),
                'filter_condition_callback' => array($this, 'filterSponsor')
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header'=> Mage::helper('catalog')->__('Depuis le'),
                'index' => 'created_at',
                'type' => 'datetime',
                'filter_index' => 'godchild.created_at',
                'filter_condition_callback' => array($this, 'filterGodchildCreatedAt')
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'website_id',
                array(
                    'header' => Mage::helper('customer')->__('Website'),
                    'align' => 'center',
                    'width' => '80px',
                    'type' => 'options',
                    'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                    'index' => 'website_id',
                )
            );
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '150',
                'type'      => 'text',
                'filter'    => false,
                'sortable'  => false,
                'renderer'  => 'GardenMedia_Sponsorship_Block_Adminhtml_Godchild_Grid_Renderer_Action'
        ));

        return parent::_prepareColumns();
    }

    /**
     * filterForRewards 
     * 
     * @param Mage_Customer_Model_Resource_Customer_Colleciton $collection : collection 
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column     : column 
     * 
     * @return GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid
     */
    public function filterForRewards($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value != '') {
            $collection->getSelect()->where($column->getFilterIndex() . ' = ?', $value);
        }
        return $this;
    }

    /**
     * filterById 
     * 
     * @param Mage_Customer_Model_Resource_Customer_Colleciton $collection : collection 
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column     : column 
     * 
     * @return GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid
     */
    public function filterById($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (!empty($value)) {
            if (isset($value['from']) && !empty($value['from'])) {
                $collection->getSelect()->where($column->getFilterIndex() . ' >= ?', $value['from']);
            }
            if (isset($value['to']) && !empty($value['to'])) {
                $collection->getSelect()->where($column->getFilterIndex() . ' <= ?', $value['to']);
            }
        }
        return $this;
    }

    /**
     * filterGodchildCreatedAt
     * 
     * @param Mage_Customer_Model_Resource_Customer_Colleciton $collection : collection 
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column     : column 
     * 
     * @return GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid
     */
    public function filterGodchildCreatedAt($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (is_array($value) && !empty($value)) {
            if (isset($value['from'])) {
                $collection->getSelect()->where($column->getFilterIndex() . ' >= ?', $value['from']->toString('YYYY-MM-dd HH:mm:ss'));
            }
            if (isset($value['to'])) {
                $collection->getSelect()->where($column->getFilterIndex() . ' <= ?', $value['to']->toString('YYYY-MM-dd HH:mm:ss'));
            }
        }
        return $this;
    }

    /**
     * getSponsorNameIndex 
     * 
     * @return void
     */
    protected function getSponsorNameIndex()
    {
        $index = 'CONCAT(';
        $index .= 'LTRIM(RTRIM(at_sponsor_firstname.value)),';
        $index .= ' \' \',';
        $index .= 'LTRIM(RTRIM(at_sponsor_lastname.value))';
        $index .= ')';
        return $index;
    }

    /**
     * filterSponsor 
     * 
     * @param Mage_Customer_Model_Resource_Customer_Colleciton $collection : collection 
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column     : column 
     * 
     * @return GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid
     */
    public function filterSponsor($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value != '') {
            $collection->getSelect()->where($column->getFilterIndex() . ' like ?', '%' . $value . '%');
        }
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/customer/edit', array('id'=>$row->getId()));
    }

    /**
     * _helper
     *
     * @return Potogan_ProductMessages_Helper_Data 
     */
    private function _helper()
    {
        return $this->helper('gm_sponsorship');
    }
}
