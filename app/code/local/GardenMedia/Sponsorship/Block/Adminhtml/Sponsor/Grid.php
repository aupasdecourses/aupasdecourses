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
 * GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sponsorGrid');
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

        $collection->getSelect()->join(
            array('sponsor' => $collection->getTable('gm_sponsorship/sponsor')),
            'e.entity_id = sponsor.sponsor_id',
            array('*')
        );

        $collection->getSelect()->joinLeft(
            array('godchild' => $collection->getTable('gm_sponsorship/godchild')),
            'sponsor.sponsor_id = godchild.sponsor_id',
            array('nb_godchild' => 'count(godchild.godchild_id)')
        );
        $collection->getSelect()->group('e.entity_id');
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
            'entity_id',
            array(
                'header' => $this->_helper()->__('Sponsor Id'),
                'type' => 'number',
                'align' => 'left',
                'index' => 'entity_id'
            )
        );

        $this->addColumn(
            'sponsor_code',
            array(
                'header'=> $this->_helper()->__('Sponsor Code'),
                'index' => 'sponsor_code',
                'filter_index' => 'sponsor.sponsor_code',
                'filter_condition_callback' => array($this, 'filterSponsor')
            )
        );

        $this->addColumn(
            'nb_godchild',
            array(
                'header'=> $this->_helper()->__('Number of Godchilds'),
                'type' => 'number',
                'index' => 'nb_godchild',
                //'filter_index' => 'count(godchild.godchild_id)',
                'filter_index' => 'nb_godchild',
                'filter_condition_callback' => array($this, 'filterGodchild')
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
            'created_at',
            array(
                'header'=> Mage::helper('catalog')->__('Sponsor since'),
                'index' => 'created_at',
                'type' => 'datetime',
                'filter_index' => 'sponsor.created_at',
                'filter_condition_callback' => array($this, 'filterSponsorCreatedAt')
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
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> 'adminhtml/customer/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
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

    /**
     * filterSponsorCreatedAt
     * 
     * @param Mage_Customer_Model_Resource_Customer_Colleciton $collection : collection 
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column     : column 
     * 
     * @return GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid
     */
    public function filterSponsorCreatedAt($collection, $column)
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
     * filterGodchild 
     * 
     * @param Mage_Customer_Model_Resource_Customer_Colleciton $collection : collection 
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column     : column 
     * 
     * @return GardenMedia_Sponsorship_Block_Adminhtml_Sponsor_Grid
     */
    public function filterGodchild($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (is_array($value) && !empty($value)) {
            if (isset($value['from'])) {
                $collection->getSelect()->having($column->getFilterIndex() . ' >= ?', $value['from']);
            }
            if (isset($value['to'])) {
                $collection->getSelect()->having($column->getFilterIndex() . ' <= ?', $value['to']);
            }
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
