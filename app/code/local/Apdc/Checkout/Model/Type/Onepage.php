<?php

/**
 * Onepage for Magento version 1.4.1.1
 */
class Apdc_Checkout_Model_Type_Onepage extends MW_Ddate_Model_Type_Onepage
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

        Mage::helper('apdc_checkout')->saveDdate($data['date'], $data['dtime'], $data['ddatei']);
        $_SESSION['ddate'] = $data['date'];
        $_SESSION['dtime'] = $data['dtime'];
        $_SESSION['ddate_comment'] = $data['ddate_comment'];

        $this->getCheckout()
            ->setStepData('ddate', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }
	
	public function saveCheckcart($data)
    {
        $this->getQuote()->setData('produit_equivalent',$data['produit_equivalent']);
        $this->getQuote()->save();
        $this->getCheckout()
            ->setStepData('checkcart', 'complete', true)
            ->setStepData('payment', 'allow', true);

        return array();
    }
	
	public function cleanQuote($items) {
		if(is_array($items) && count($items) > 0) {
			foreach ($items as $item) {
				$this->getQuote()->removeItem($item);
			}
			$this->getQuote()->save();
		}
		return array();
	}
	
	public function saveComment($item, $comment) {
		$comment = htmlentities($comment, ENT_QUOTES, 'UTF-8');
		$item = $this->getQuote()->getItemById($item);
		$item->setItemComment($comment)->save();
		$this->getQuote()->save();
		return array();
	}
}
