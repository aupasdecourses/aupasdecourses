<?php

/**
 * This file is part of the GardenMedia Mission Project
 *
 * @category Apdc
 * @package  Dataflow
 * @author   Erwan INYZANT <erwan@garden-media.fr>
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Dataflow_Block_Adminhtml_System_Convert_Profile_Edit_Tab_Run
 *
 * @category Apdc
 * @package  Dataflow
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Run
 * @author   Erwan INYZANT <erwan@garden-media.fr>
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Dataflow_Block_Adminhtml_System_Convert_Profile_Edit_Tab_Run extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Run
{
    public function __construct()
    {
        parent::__construct();
        if ($this->isAdvancedProfile()) {
            $this->setTemplate('apdc/apdc_dataflow/system/convert/profile/run.phtml');
        }
    }

    public function getRunButtonHtml()
    {
        $html = parent::getRunButtonHtml();

        if ($this->isAdvancedProfile()) {
            $form = new Varien_Data_Form();
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => $this->_helper()->__('Filtrage')]);
            $fieldset->addField(
                'store',
                'select',
                [
                    'name' => 'filters[store]',
                    'label' => $this->_helper()->__('Store'),
                    'title' => $this->_helper()->__('Store'),
                    'class' => 'profile_filter',
                    'values' => $this->getStoresOptions(),
                    'required' => false,
                ]
            );
            $fieldset->addField(
                'commercant',
                'select',
                [
                    'name' => 'filters[commercant]',
                    'label' => $this->_helper()->__('Commercant'),
                    'title' => $this->_helper()->__('Commercant'),
                    'class' => 'profile_filter',
                    'values' => $this->getCommercantsOptions(),
                    'required' => false
                ]
            );
            $fieldset->addField(
                'code_ref_apdc',
                'select',
                [
                    'name' => 'filters[code_ref_apdc]',
                    'label' => $this->_helper()->__('Code Référentiel'),
                    'title' => $this->_helper()->__('Code Référentiel'),
                    'class' => 'profile_filter',
                    'values' => $this->getAttributeProductOptions(
                        [
                            'label'=>'code_ref_apdc',
                            'value'=>'code_ref_apdc',
                        ]
                    ),
                    'required' => false
                ]
            );
            $fieldset->addField(
                'cat_parent',
                'select',
                [
                    'name' => 'filters[cat_parent]',
                    'label' => $this->_helper()->__('Catégorie Parent'),
                    'title' => $this->_helper()->__('Catégorie Parent'),
                    'class' => 'profile_filter',
                    'values' => $this->getAttributeProductOptions(
                        [
                            'label'=>'cat_parent',
                            'value'=>'cat_parent'
                        ]
                    ),
                    'required' => false
                ]
            );

            $fieldset->addField(
                'nom_cat',
                'select',
                [
                    'name' => 'filters[nom_cat]',
                    'label' => $this->_helper()->__('Catégorie Principale'),
                    'title' => $this->_helper()->__('Catégorie Principale'),
                    'class' => 'profile_filter',
                    'values' => $this->getAttributeProductOptions(
                        [
                            'label' => 'nom_cat',
                            'value' => 'nom_cat'
                        ]
                    ),
                    'required' => false
                ]
            );

            $fieldset->addField(
                'nom_sous_cat',
                'select',
                [
                    'name' => 'filters[nom_sous_cat]',
                    'label' => $this->_helper()->__('Sous Catégorie'),
                    'title' => $this->_helper()->__('Sous Catégorie'),
                    'class' => 'profile_filter',
                    'values' => $this->getAttributeProductOptions(
                        [
                            'label' => 'nom_sous_cat',
                            'value' => 'nom_sous_cat'
                        ]
                    ),
                    'required' => false
                ]
            );

            $fieldset->addField(
                'producteur',
                'select',
                [
                    'name' => 'filters[producteur]',
                    'label' => $this->_helper()->__('Producteur'),
                    'title' => $this->_helper()->__('Producteur'),
                    'class' => 'profile_filter',
                    'values' => $this->getAttributeProductOptions(
                        [
                            'label' => 'producteur',
                            'value' => 'producteur'
                        ]
                    ),
                    'required' => false
                ]
            );
            
            $fieldset->addField(
                'produit_biologique',
                'select',
                [
                    'name' => 'filters[produit_biologique]',
                    'label' => $this->_helper()->__('Produit Bio'),
                    'title' => $this->_helper()->__('Produit Bio'),
                    'class' => 'profile_filter',
                    'values' => $this->getAttributeProductOptions(
                        [
                            'label' => 'produit_biologique',
                            'value' => 'produit_biologique'
                        ]
                    ),
                    'required' => false
                ]
            );


            $html = $form->toHtml() . $html;
        }

        return $html;
    }

    /**
     * isAdvancedProfile
     *
     * @return boolean
     */
    protected function isAdvancedProfile()
    {
        if (Mage::app()->getRequest()->getControllerName() == 'system_convert_profile') {
            return true;
        }
        return false;
    }

    /**
     * getCommercantsOptions
     *
     * @return array
     */
    protected function getCommercantsOptions()
    {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('catalog_product', 'commercant');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        $options = $attribute->getSource()->getAllOptions(false);
        array_unshift($options, ['label' => $this->_helper()->__('-- Tous les commerçants --'), 'value' => '']);
        return $options;
    }

    /**codeoptions
     * getStoresOptions
     *
     * @return array
     */
    protected function getStoresOptions()
    {
        $storesOptions = [
            [
                'label' => $this->_helper()->__('-- Tous les stores --'),
                'value' => ''
            ]
        ];
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            $storesOptions[] = [
                'label' => $store->getName(),
                'value' => $store->getId()
            ];
        }
        return $storesOptions;
    }

    /**
     * getAttributeRefOptions
     *
     * @return array
     */
    protected function getAttributeRefOptions(Array $config)
    {
        $codeoptions = [
            [
                'label' => $this->_helper()->__('-- Tous --'),
                'value' => ''
            ]
        ];
        foreach($config as $c){
            if(!in_array($c,Mage::getResourceModel('apdc_referentiel/referentiel')->getFields()))
            {
                return false;
            }
        }
        
        $coderef = Mage::getModel('apdc_referentiel/referentiel')->getCollection()->addFieldToSelect($config)->addGroupByFilter($config['value']);
        foreach ($coderef as $c) {
            $codeoptions[] = [
                'label' => $c->getData('label'),
                'value' => $c->getData('value')
            ];
        }
        return $codeoptions;
    }

    /**
     * getAttributeProductOptions
     *
     * @return array
     */
    protected function getAttributeProductOptions(Array $config)
    {
       
        $coderef = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect($config)->load();
        $done=array();
        foreach ($coderef as $c) {
            if (!in_array($c->getData($config['value']), $done) && $c->getData($config['value'])<>"") {
                $codeoptions[$c->getData($config['value'])] = [
                    'label' => $c->getData($config['label']),
                    'value' => $c->getData($config['value'])
                ];
            array_push($done,$c->getData($config['label']));
            }
        }
        ksort($codeoptions);

        array_unshift($codeoptions,[
                'label' => $this->_helper()->__('-- Tous --'),
                'value' => ''
            ]
        );

        return $codeoptions;
    }

    /**
     * _helper
     *
     * @return Apdc_Dataflow_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('apdc_dataflow');
    }
}
