<?php

namespace Apdc\ApdcBundle\Services\Helpers;

trait Model
{

	/** Mettre dans trait Model **/
    private function checkEntryToModel($model, array $filters)
    {
        $entry = $model->getCollection();
        foreach ($filters as $k => $v) {
            $entry->addFieldToFilter($k, $v);
        }
        if ($entry->getFirstItem()->getId() != null) {
            return true;
        } else {
            return false;
        }
    }

    /** Mettre dans trait Model **/
    private function addEntryToModel($model, $data, $updatedFields)
    {
        foreach ($data as $k => $v) {
            $model->setData($k, $v);
        }
        foreach ($updatedFields as $k => $v) {
            $model->setData($k, $v);
        }
        $model->save();
    }

    /** Mettre dans trait Model **/
    private function updateEntryToModel($model, array $filters, array $updatedFields)
    {
        $entry = $model->getCollection();
        foreach ($filters as $k => $v) {
            $entry->addFieldToFilter($k, $v);
        }
        if (($id = $entry->getFirstItem()->getId()) != null) {
            $model->load($id);
            foreach ($updatedFields as $k => $v) {
                $model->setData($k, $v);
            }
            $model->save();
        } else {
            $this->addEntryToModel($model, $updatedFields);
        }
    }

    /** Mettre dans trait Model **/
    public function addEntryToOrderField(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel('amorderattach/order_field'),
            $data
        );
    }

    /** Mettre dans trait Model **/
    public function updateEntryToOrderField(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('amorderattach/order_field');
        $check = $this->checkEntryToModel($model, $filters);

        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }

    /** Mettre dans trait Model **/
    public function addEntryToRefundItem(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')),
            $data
        );
    }

    /** Mettre dans trait Model **/
    public function updateEntryToRefundItem(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/refund_items');
        $check = $this->checkEntryToModel($model, $filters);

        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }

    /** Mettre dans trait Model **/
    public function addEntryToBillingDetails(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/indi_billingdetails')),
            $data
        );
    }

    /** Mettre dans trait Model **/
    public function updateEntryToBillingDetails(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/indi_billingdetails');
        $check = $this->checkEntryToModel($model, $filters);
        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }

    /** Mettre dans trait Model **/
    public function addEntryToBillingSummary(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/indi_billingsummary')),
                $data
        );
    }

    public function addEntryToGeocode(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel('pmainguet_delivery/geocode_customers'),
            $data
        );
    }

    /** Mettre dans trait Model **/
    public function updateEntryToBillingSummary(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/indi_billingsummary');
        $check = $this->checkEntryToModel($model, $filters);
        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }

    /** Mettre dans trait Model **/
    public function updateEntryToGeocode(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/geocode_customers');
        $check = $this->checkEntryToModel($model, $filters);
        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }
	
}