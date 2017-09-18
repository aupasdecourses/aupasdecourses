<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Contact_Grid
 */
class Apdc_Commercant_Block_Adminhtml_Contact_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('contactGrid');
        $this->setDefaultSort('lastname');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /** @var Apdc_Commercant_Model_Resource_Contact_Collection $collection */
        $collection = Mage::getModel('apdc_commercant/contact')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header' => $this->__('Id'),
            'index' => 'id_contact',
        ]);

        $this->addColumn('lastname', [
            'header' => $this->__('Nom'),
            'index' => 'lastname',
        ]);

        $this->addColumn('firstname', [
            'header' => $this->__('Prénom'),
            'index' => 'firstname',
        ]);

        $this->addColumn('email', [
            'header' => $this->__('Email'),
            'index' => 'email',
        ]);

        $this->addColumn('phone', [
            'header' => $this->__('Téléphone'),
            'index' => 'phone',
        ]);

        //$this->addColumn('role_id', [
        //    'header' => $this->__('Rôles'),
        //    'index' => 'role_id',
        //    'renderer'  => 'Apdc_Commercant_Block_Adminhtml_Contact_Renderer_Role',
        //]);

        parent::_prepareColumns();
    }

    /**
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id_contact' => $row->getId()]);
    }
}
