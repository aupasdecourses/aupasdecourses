<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Shop_Grid
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('shopGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /** @var Apdc_Commercant_Model_Resource_Shop_Collection $collection */
        $collection = Mage::getModel('apdc_commercant/shop')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header' => $this->__('Id Magasin'),
            'index' => 'id_shop',
        ]);

        $this->addColumn('postcode', [
            'header' => $this->__('Code Postal'),
            'index' => 'postcode',
        ]);

        $this->addColumn('code', [
            'header' => $this->__('Code'),
            'index' => 'code',
        ]);

        $this->addColumn('shop_type', [
            'header' => $this->__('Type Magasin'),
            'index' => 'shop_type',
            //'renderer'  => 'Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Typeshop',
        ]);

        $this->addColumn('name', [
            'header' => $this->__('Nom'),
            'index' => 'name',
        ]);

        $this->addColumn('enabled', [
            'header' => $this->__('Activé'),
            'index' => 'enabled',
            'renderer'  => 'Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Enabled',
        ]);

        $this->addColumn('category_ids', [
            'header' => $this->__('Catégorie'),
            'index' => 'category_ids',
            'renderer'  => 'Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Category',
        ]);

        $this->addColumn('id_commercant', [
            'header' => $this->__('Commercant'),
            'index' => 'id_commercant',
            'renderer'  => 'Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Commercant'
        ]);

        $this->addColumn('id_attribut_commercant', [
            'header' => $this->__('Attributs Produits "commercant"'),
            'index' => 'id_attribut_commercant',
            'renderer'  => 'Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Productattribute',
        ]);

        $this->addColumn('id_contact_manager', [
            'header' => $this->__('Manager magasin'),
            'index' => 'id_contact_manager',
            'renderer'  => 'Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Contact',
        ]);

        parent::_prepareColumns();
    }

    /**
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id_shop' => $row->getId()]);
    }
}
