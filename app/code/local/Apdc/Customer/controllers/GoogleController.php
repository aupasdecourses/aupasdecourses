<?php


class Apdc_Customer_GoogleController extends Mage_Core_Controller_Front_Action
{
    public function ajaxLoginAction()
    {
        $params = $this->getRequest()->getPost();
        if (isset($params['isAjax']) && $params['isAjax'] == 1) {
            $response = [];
            try {
                if (isset($params['token']) && !empty($params['token']) &&
                    isset($params['fields']) && !empty($params['fields'])
                ) {

                    $helper = Mage::helper('inchoo_socialconnect/google');

                    $params['token']['created'] = time();
                    $token = json_encode($params['token']);
                    $info = new Varien_Object();
                    $info->setData($params['fields']);
                    $customersByGoogleId = $helper->getCustomersByGoogleId($info->getId());
                    $customersByGoogleId->addAttributeToSelect('customer_neighborhood');

                    if($customersByGoogleId->getSize()) {
                        // Existing connected user - login
                        $customer = $customersByGoogleId->getFirstItem();

                        $helper->loginByCustomer($customer);

                        Mage::getSingleton('core/session')
                            ->addSuccess(
                                $this->__('You have successfully logged in using your Google account.')
                            );

                        $response['status'] = 'SUCCESS';
                        if ($customer->getCustomerNeighborhood()) {
                            $response['redirect'] = $customer->getNeighborhoodUrl();
                        } else {
                            $response['need_to_choose_neighborhood'] = 1;
                            $response['html'] = $this->_getLayout('apdc_choose_neighborhood');
                        }
                    } else {

                        $customersByEmail = $helper->getCustomersByEmail($info->getEmail());
                        $customersByEmail->addAttributeToSelect('customer_neighborhood');

                        if($customersByEmail->getSize()) {
                            // Email account already exists - attach, login
                            $customer = $customersByEmail->getFirstItem();

                            $helper->connectByGoogleId(
                                $customer,
                                $info->getId(),
                                $token
                            );

                            Mage::getSingleton('core/session')->addSuccess(
                                $this->__('We have discovered you already have an account at our store. Your Google account is now connected to your store account.')
                            );
                            $response['status'] = 'SUCCESS';
                            if ($customer->getCustomerNeighborhood()) {
                                $response['redirect'] = $customer->getNeighborhoodUrl();
                            } else {
                                $response['need_to_choose_neighborhood'] = 1;
                                $response['html'] = $this->_getLayout('apdc_choose_neighborhood');
                            }
                        } else {

                            // New connection - create, attach, login
                            $givenName = $info->getGivenName();
                            if(empty($givenName)) {
                                throw new Exception(
                                    $this->__('Sorry, could not retrieve your Google first name. Please try again.')
                                );
                            }

                            $familyName = $info->getFamilyName();
                            if(empty($familyName)) {
                                throw new Exception(
                                    $this->__('Sorry, could not retrieve your Google last name. Please try again.')
                                );
                            }

                            $helper->connectByCreatingAccount(
                                $info->getEmail(),
                                $info->getGivenName(),
                                $info->getFamilyName(),
                                $info->getId(),
                                $token
                            );

                            Mage::getSingleton('core/session')->addSuccess(
                                $this->__('Your Google account is now connected to your new user account at our store. Now you can login using our Google Login button.')
                            );

                            $response['status'] = 'SUCCESS';
                            $response['need_to_choose_neighborhood'] = 1;
                            $response['html'] = $this->_getLayout('apdc_choose_neighborhood');
                        }
                    }
                }
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $response['html'] = $this->_getLayout('apdc_login_view');
            }
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }

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
}
