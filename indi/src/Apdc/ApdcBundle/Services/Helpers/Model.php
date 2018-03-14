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

    public function addEntryToOrderField(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel('amorderattach/order_field'),
            $data
        );
    }

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

    public function addEntryToRefundItem(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')),
            $data
        );
    }

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

    public function addEntryToRefundPricevariation(array $data)
    {
        $model = \Mage::getModel('pmainguet_delivery/refund_pricevariation');

        try {
            $model->setData($data)->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function updateEntryToRefundPricevariation(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/refund_pricevariation');
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

    public function addEntryToBillingDetails(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/indi_billingdetails')),
            $data
        );
    }

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

    public function addEntryToMistralDelivery(array $data)
    {
        $this->addEntryToModel(\Mage::getModel('pmainguet_delivery/indi_mistraldelivery'), $data);
    }
    public function updateEntryToMistralDelivery(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/indi_mistraldelivery');
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

    public function getMistralDelivery()
    {
        $collection = \Mage::getModel('pmainguet_delivery/indi_mistraldelivery')->getCollection();
        return $collection;
    }
	
}