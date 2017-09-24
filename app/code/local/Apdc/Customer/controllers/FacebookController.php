<?php


/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Customer
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Customer_FacebookController 
 * 
 * @category Apdc
 * @package  Customer
 * @uses     Mage
 * @uses     Mage_Core_Controller_Front_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Customer_FacebookController extends Mage_Core_Controller_Front_Action
{
    public function ajaxLoginAction()
    {
        $params = $this->getRequest()->getPost();
        if (isset($params['isAjax']) && $params['isAjax'] == 1) {
            $response = [];
            try {
                if (isset($params['status']) && $params['status'] == 'connected') {
                    if (isset($params['fields']) && !empty($params['fields'])) {

                        $helper = Mage::helper('inchoo_socialconnect/facebook');

                        $token = json_decode(json_encode($params['token']), FALSE);

                        $info = new Varien_Object();
                        $info->setData($params['fields']);
                        $customersByFacebookId = $helper->getCustomersByFacebookId($info->getId());
                        $customersByFacebookId->addAttributeToSelect('customer_neighborhood');

                        if($customersByFacebookId->getSize()) {
                            // Existing connected user - login
                            $customer = $customersByFacebookId->getFirstItem();

                            $helper->loginByCustomer($customer);

                            Mage::getSingleton('core/session')
                                ->addSuccess(
                                    $this->__('You have successfully logged in using your Facebook account.')
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

                                $helper->connectByFacebookId(
                                    $customer,
                                    $info->getId(),
                                    $token
                                );

                                Mage::getSingleton('core/session')->addSuccess(
                                    $this->__('We have discovered you already have an account at our store. Your Facebook account is '.
                                    'now connected to your store account.')
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
                                $firstName = $info->getFirstName();
                                if(empty($firstName)) {
                                    throw new Exception(
                                        $this->__('Sorry, could not retrieve your Facebook first name. Please try again.')
                                    );
                                }

                                $lastName = $info->getLastName();
                                if(empty($lastName)) {
                                    throw new Exception(
                                        $this->__('Sorry, could not retrieve your Facebook last name. Please try again.')
                                    );
                                }

                                $birthday = $info->getBirthday();
                                $birthday = Mage::app()->getLocale()->date($birthday, null, null, false)
                                    ->toString('yyyy-MM-dd');

                                $gender = $info->getGender();
                                if(empty($gender)) {
                                    $gender = null;
                                } else if($gender == 'male') {
                                    $gender = 1;
                                } else if($gender == 'female') {
                                    $gender = 2;
                                }

                                $helper->connectByCreatingAccount(
                                    $info->getEmail(),
                                    $info->getFirstName(),
                                    $info->getLastName(),
                                    $info->getId(),
                                    $birthday,
                                    $gender,
                                    $token
                                );

                                Mage::getSingleton('core/session')->addSuccess(
                                    $this->__('Your Facebook account is now connected to your new user account at our store.'.
                                    ' Now you can login using our Facebook Login button.')
                                );

                                $response['status'] = 'SUCCESS';
                                $response['need_to_choose_neighborhood'] = 1;
                                $response['html'] = $this->_getLayout('apdc_choose_neighborhood');
                            }
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
