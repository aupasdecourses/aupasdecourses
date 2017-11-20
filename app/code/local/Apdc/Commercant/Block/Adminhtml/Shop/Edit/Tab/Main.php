<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_Main 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('shop');
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'base',
            ['legend' => $this->__('Général')]
        );

        if ($model->getId()) {
            $fieldset->addField('id_shop', 'hidden', ['name' => 'id_shop']);
        }

        $fieldset->addField('enabled', 'select', [
            'name' => 'enabled',
            'label' => $this->__('Activé'),
            'required' => false,
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'disabled' => $isElementDisabled
        ]);
        $commercants = Mage::getModel('apdc_commercant/commercant')->getCollection()->toOptionArray();
        array_unshift($commercants, ['value' => '', 'label' => '']);
        $fieldset->addField('id_commercant', 'select', [
            'name' => 'id_commercant',
            'label' => $this->__('Commercant (entité légale)'),
            'required' => true,
            'values' => $commercants,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => $this->__('Nom'),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('code', 'text', [
            'name' => 'code',
            'label' => $this->__('Code'),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('website', 'text', [
            'name' => 'website',
            'label' => $this->__('Site internet'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('blacklist', 'select', [
            'name' => 'blacklist',
            'label' => $this->__('Dans Blacklist'),
            'note' => $this->__('Historique de problèmes de préparation de commandes, si oui, à appeler systématiquement pour vérification.'),
            'required' => false,
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'disabled' => $isElementDisabled,
        ]);

        $fieldset = $form->addFieldset(
            'finance',
            ['legend' => $this->__('Admin & Finance')]
        );

        $fieldset->addField('siret', 'text', [
            'name' => 'siret',
            'label' => $this->__('SIRET'),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('vat_number', 'text', [
            'name' => 'vat_number',
            'label' => $this->__('N° TVA'),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('cpte_hipay', 'text', [
            'name' => 'cpte_hipay',
            'label' => $this->__('N° Compte Hipay'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('cpte_compta', 'text', [
            'name' => 'cpte_compta',
            'label' => $this->__('N° Compte dans Comptabilité APDC'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('email_hipay', 'text', [
            'name' => 'email_hipay',
            'label' => $this->__('Email Hipay'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('mdp_hipay', 'text', [
            'name' => 'mdp_hipay',
            'label' => $this->__('Mot de Passe Hipay'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $fieldset = $form->addFieldset(
            'contacts',
            ['legend' => $this->__('Contacts')]
        );

        $availableManagers = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addRoleFilter(Apdc_Commercant_Model_Source_Contact_Type::TYPE_MANAGER)
            ->toOptionArray();
        array_unshift($availableManagers, ['value' => '', 'label' => '']);
        $fieldset->addField('id_contact_manager', 'select', [
            'name' => 'id_contact_manager',
            'label' => $this->__('Contact manager magasin'),
            'required' => true,
            'values' => $availableManagers,
            'disabled' => $isElementDisabled
        ]);

        $availableEmployees = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addRoleFilter(Apdc_Commercant_Model_Source_Contact_Type::TYPE_EMPLOYEE)
            ->toOptionArray();
        array_unshift($availableEmployees, ['value' => '', 'label' => '']);
        $fieldset->addField('id_contact_employee', 'select', [
            'name' => 'id_contact_employee',
            'label' => $this->__('Contact employé n°1'),
            'required' => false,
            'values' => $availableEmployees,
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('id_contact_employee_bis', 'select', [
            'name' => 'id_contact_employee_bis',
            'label' => $this->__('Contact employé n°2'),
            'required' => false,
            'values' => $availableEmployees,
            'disabled' => $isElementDisabled
        ]);

        $values = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', 'commercant')
            ->getSource()
            ->getAllOptions();
        $fieldset->addField('id_attribut_commercant', 'select', [
            'name' => 'id_attribut_commercant',
            'label' => $this->__('Attribut Produits "commercant"'),
            'required' => true,
            'values' => $values,
            'note' => $this->__('L\'option correspondant au magasin doit avoir été créé au préalable dans Catalogue > Attributs. La valeur de l\'option doit être identique - casse comprise - au nom du magasin (et pas du commerçant, entité légale!)'),
            'disabled' => $isElementDisabled
        ]);

        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
        } else {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * getTableLabel
     * 
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__('Informations Générales');
    }

    /**
     * getTabTitle
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('Informations Générales');
    }

    /**
     * canShowTab
     * 
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * isHidden
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * _helper 
     * 
     * @return Apdc_Neighborhood_Helper_Data
     */
    private function _helper()
    {
        return Mage::helper('apdc_commercant');
    }

    /**
     * _isAllowedAction
     * 
     * @param string $action : action
     * 
     * @return boolean
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/shop/' . $action);
    }
}
