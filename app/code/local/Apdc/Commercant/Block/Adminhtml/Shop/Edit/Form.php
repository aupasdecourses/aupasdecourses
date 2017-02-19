<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Form
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('shop');

        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ]
        );

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
        ]);
        $commercants = Mage::getModel('apdc_commercant/commercant')->getCollection()->toOptionArray();
        array_unshift($commercants, ['value' => '', 'label' => '']);
        $fieldset->addField('id_commercant', 'select', [
            'name' => 'id_commercant',
            'label' => $this->__('Commercant (entité légale)'),
            'required' => true,
            'values' => $commercants,
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => $this->__('Nom'),
            'required' => true,
        ]);

        $fieldset->addField('code', 'text', [
            'name' => 'code',
            'label' => $this->__('Code'),
            'required' => true,
        ]);

        $fieldset->addField('website', 'text', [
            'name' => 'website',
            'label' => $this->__('Site internet'),
            'required' => false,
        ]);

        $fieldset->addField('siret', 'text', [
            'name' => 'siret',
            'label' => $this->__('SIRET'),
            'required' => true,
        ]);

        $fieldset->addField('vat_number', 'text', [
            'name' => 'vat_number',
            'label' => $this->__('N° TVA'),
            'required' => true,
        ]);

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
        ]);
        $fieldset->addField('id_contact_employee_bis', 'select', [
            'name' => 'id_contact_employee_bis',
            'label' => $this->__('Contact employé n°2'),
            'required' => false,
            'values' => $availableEmployees,
        ]);

        $commercantCategories = Mage::getModel('catalog/category')
            ->getCollection()
            ->setOrder('name')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('estcom_commercant', 70);
        $values = [];
        $S = Mage::helper('apdc_commercant')->getStoresArray();
        foreach ($commercantCategories as $category) {
            $storename=$S[explode('/', $category->getPath())[1]]['name'];
            $values[]=['value'=>$category->getId(), 'label' => $category->getName().' - '.$storename];
        }

        $fieldset->addField('id_category', 'multiselect', [
            'name' => 'id_category',
            'label' => $this->__('Categorie(s)'),
            'required' => true,
            'values' => $values,
            'note' => $this->__('Catégorie(s) correspondante(s) aux produits du magasin'),
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
        ]);


        $fieldset = $form->addFieldset(
            'address',
            ['legend' => $this->__('Adresse')]
        );

        $fieldset->addField('street', 'text', [
            'name' => 'street',
            'label' => $this->__('Rue'),
            'required' => true,
        ]);

        $fieldset->addField('postcode', 'text', [
            'name' => 'postcode',
            'label' => $this->__('Code Postal'),
            'required' => true,
        ]);

        $fieldset->addField('city', 'text', [
            'name' => 'city',
            'label' => $this->__('Ville'),
            'required' => true,
        ]);

        $fieldset->addField('phone', 'text', [
            'name' => 'phone',
            'label' => $this->__('Téléphone'),
            'required' => true,
        ]);

        $this->_addTimetable($form);

        $fieldset = $form->addFieldset(
            'closing_period',
            ['legend' => $this->__('Fermetures & Vacances')]
        );
        $fieldset->addType('closing_periods', Mage::getConfig()->getBlockClassName('apdc_commercant/adminhtml_form_element_closing'));
        $fieldset->addField('closing_periods', 'closing_periods', [
            'name' => 'closing_periods',
            'label' => $this->__('Périodes de fermeture'),
        ]);

        $fieldset = $form->addFieldset(
            'google',
            ['legend' => $this->__('Tableau produits Google Sheets')]
        );

        $fieldset->addField('google_id', 'text', [
            'name' => 'google_id',
            'label' => $this->__('Google id'),
            'required' => false,
        ]);

        $fieldset->addField('google_key', 'text', [
            'name' => 'google_key',
            'label' => $this->__('Google key'),
            'required' => false,
        ]);

        $data = $model->getData();
        if (empty($data['timetable'])) {
            foreach (Mage::helper('apdc_commercant')->getDays() as $index => $day) {
                $data['timetable_'.$index] = '9:00-20:00';
            }
        } else {
            foreach ($data['timetable'] as $index => $dayData) {
                $data['timetable_'.$index] = $dayData;
            }
        }
        $form->setValues($data);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param Varien_Data_Form $form
     */
    protected function _addTimetable(Varien_Data_Form $form)
    {
        $timetableFieldset = $form->addFieldset(
            'timetable_fieldset',
            [
                'legend' => $this->__('Horaires d\'ouverture'),
            ]
        );
        $timetableFieldset->addField('timetable_hint', 'note', [
            'text' => $this->__('Format attendu: hh:mm-hh:mm, ie 9:30-17:00. Laisse VIDE si le magasin est fermé.')
        ]);
        $days = Mage::helper('apdc_commercant')->getDays();
        $deliveryDaysValues = [];
        foreach ($days as $index => $day) {
            $timetableFieldset->addField('timetable_'.$index, 'text', [
                'name' => 'timetable['.$index.']',
                'label' => $this->__($day),
                'required' => false,
            ]);

            $deliveryDaysValues[] = ['value' =>  $index + 1, 'label' => $this->__($day)];
        }

        $timetableFieldset->addField('delivery_days', 'multiselect', [
            'name' => 'delivery_days',
            'label' => $this->__('Jour de livraison APDC'),
            'required' => false,
            'values' => $deliveryDaysValues,
            'note'=>$this->__('Correspond au jour où APDC peut réaliser des livraisons.'),
        ]);
    }
}
