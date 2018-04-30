<?php

class Apdc_Home_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _getLayout($id)
    {
        $this->getLayout()->getUpdate()->addHandle(array('default', $id));

        $this->loadLayout();

        $this->getLayout()->removeOutputBlock('root')->addOutputBlock('content');
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();

        return $this->getLayout()->getOutput();
    }

    public function ajaxPopupViewAction()
    {
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $response = array();
            try {
                $response['html'] = $this->_getLayout($params['handle']);
                $response['status'] = 'SUCCESS';
            } catch (Mage_Core_Exception $e) {
                $msg = '';
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $msg .= $message.'<br/>';
                }

                $response['status'] = 'ERROR';
                $response['message'] = $msg;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot find template.');
                Mage::logException($e);
            }
            if (!empty($response)) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }

            return;
        }
    }

	public function redirectAjaxAction()
    {
		$data = $this->getRequest()->getPost();

		$response=array();

		if(isset($data['isAjax'])&&$data['isAjax']==1){
			if ($data['medium'] == 'zipcode' && isset($data['zipcode'])) {
                $store = Mage::helper('apdc_neighborhood')->getNeighborhoodByPostcode($data['zipcode']);
                Mage::helper('apdc_neighborhood')->setNeighborhood($store);
                $url = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
				$response['status'] = 1;
				$response['redirect'] = 1;
				$response['redirectURL'] = $url;
			} elseif ($data['medium'] == 'select'){
                $store = Mage::app()->getWebsite($data['website'])->getDefaultStore();
                Mage::helper('apdc_neighborhood')->setNeighborhood($store);
                $url = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                $response['status'] = 1;
                $response['redirect'] = 1;
                $response['redirectURL'] = $url;
			}
		}

		$this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
}
