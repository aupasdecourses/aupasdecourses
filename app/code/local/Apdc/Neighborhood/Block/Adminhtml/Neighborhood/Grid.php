<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Grid 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('neighborhoodGrid');
        $this->setDefaultSort('entity_id');
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
        $collection = Mage::getModel('apdc_neighborhood/neighborhood')->getcollection()
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('website_id')
            ->addFieldToSelect('code_do');
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

        $this->addColumn('website_id', array(
            'header'    => Mage::helper('customer')->__('Website'),
            'align'     => 'center',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
            'index'     => 'website_id',
        ));

        $this->addColumn(
            'code_do',
            array(
                'header'=> Mage::helper('catalog')->__('Code Do'),
                'index' => 'code_do',
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
     * @return Apdc_Neighborhood_Helper_Data 
     */
    private function _helper()
    {
        return $this->helper('apdc_neighborhood');
    }

    /**
     * getWebsitesOptions 
     * 
     * @return array
     */
    protected function getWebsitesOptions()
    {
        $options = array();
        foreach (Mage::app()->getWebsites() as $website) {
            if ($website->getId() > 0) {
                $options[$website->getId()] = $website->getName();
            }
        }
        return $options;
    }
}
