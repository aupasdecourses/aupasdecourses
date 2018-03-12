<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_Block_Adminhtml_Partner_Grid 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Block_Adminhtml_Partner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('partnerGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * _prepareCollection 
     * 
     * @return void
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('apdc_partner/partner')->getcollection()
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('is_active')
            ->addFieldToSelect('name')
            ->addFieldToSelect('email');
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
                'header' => $this->_helper()->__('Id'),
                'type' => 'number',
                'align' => 'left',
                'index' => 'entity_id'
            )
        );

        $this->addColumn(
            'is_active',
            array(
                'header'=> $this->_helper()->__('Is Active'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => array(0 => $this->_helper()->__('No'), 1 => $this->_helper()->__('Yes'))
            )
        );

        $this->addColumn(
            'name',
            array(
                'header'=> $this->_helper()->__('Name'),
                'index' => 'name',
            )
        );

        $this->addColumn(
            'email',
            array(
                'header'=> $this->_helper()->__('Email'),
                'index' => 'email',
            )
        );


        return parent::_prepareColumns();
    }

    /**
     * getRowUrl
     * 
     * @param mixed $row row
     * 
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * _helper
     *
     * @return Apdc_Partner_Helper_Data 
     */
    private function _helper()
    {
        return $this->helper('apdc_partner');
    }
}
