<?php

class Apdc_Front_NewsletterController extends Mage_Core_Controller_Front_Action
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
        if (isset($params) && $params['isAjax'] == 1) {
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

    public function ajaxNewsletterProcessAction()
    {
        $params = $this->getRequest()->getPost();

        if (isset($params) && $params['isAjax'] == 1) {
            $storeId = Mage::app()->getStore()->getStoreId();

            $api = [
                'login' => 'aupasdecourses',
                'url' => 'https://us10.api.mailchimp.com/3.0/',
                'key' => Mage::helper('monkey')->getApiKey($storeId),
                'target' => 'lists/'.$params['id'].'/members/',
            ];

            $ch = curl_init($api['url'].$api['target']);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: '.$api['login'].' '.$api['key'],
            ));

            $data=array(
                'email_address' => $params['EMAIL'],
                'status' => 'subscribed'
            );

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'YOUR-USER-AGENT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response);

            $return=array();

            $return['status']=$response->{'status'};

             if($return['status']=='subscribed'){
                $return['email_address']=$response->{'email_address'};
                Mage::getSingleton('core/session')->addSuccess('Vous avez été enregistré!');
            } else {
                Mage::getSingleton('core/session')->addError('Vous n\'avez pas été enregistré ...');
            }

            $return['html']=$this->_getLayout('apdc_newsletter_view');

            if (!empty($return)) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
            }

            return;
        }
    }
}
