<?php

/**
 * Onepage for Magento version 1.4.1.1
 */
class Pmainguet_CustomCheckout_Model_Type_Onepage extends MW_Ddate_Model_Type_Onepage
{
    public function saveDdate($data)
    {
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid Delivery Date.')
            );
            return $res;
        }

        if (empty($data['date'])) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Please select Delivery Date!')
            );
            return $res;
        }

        if (empty($data['dtime'])) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Please select Delivery Time!')
            );
            return $res;
        }

        $this->getQuote()->setDdate($data['date']);
        $this->getQuote()->setDtime(Mage::getModel('ddate/dtime')->load($data['dtime'])->getDtime());
        $this->getQuote()->setDdateComment($data['ddate_comment']);
        $this->getQuote()->save();

        //Pierre Mainguet - added for compatibility Pmainguet_Attributeqtoi_Model_Observer.php
        Mage::getSingleton('core/session')->setDdate($data['date']);

        $_SESSION['ddate'] = $data['date'];
        $_SESSION['dtime'] = $data['dtime'];
        $_SESSION['ddate_comment'] = $data['ddate_comment'];

        $this->getCheckout()
            ->setStepData('ddate', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }
}
