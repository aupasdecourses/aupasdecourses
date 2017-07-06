<?php

class Apdc_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('ajaxloginprocess', 'ajaxregisterprocess');
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get Helper.
     *
     * @param string $path
     *
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($path)
    {
        return Mage::helper($path);
    }

    /**
     * Get model by path.
     *
     * @param string $path
     * @param array|null $arguments
     *
     * @return false|Mage_Core_Model_Abstract
     */
    public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
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

    /**
     * Define target URL and redirect customer after logging in.
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl($this->_getHelper('customer')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = $this->_getModel('core/url')
                            ->getRebuiltUrl($this->_getHelper('core')->urlDecodeAndEscape($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } elseif ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl($this->_getHelper('customer')->getLoginUrl());
            }
        } elseif ($session->getBeforeAuthUrl() ==  $this->_getHelper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl($this->_getHelper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        return $session->getBeforeAuthUrl(true);
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

    public function ajaxLoginProcessAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');

            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');

            return;
        }

        $session = $this->_getSession();

        $params = $this->getRequest()->getPost();
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $response = array();
        if ($params['isAjax'] == 1) {
            $login = $params['login'];

            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $response = array();

            if (!$this->_validateFormKey()) {
                $response['status'] = 'ERROR';
                $response['message'] = Mage::helper('customer')->__('Invalid form key');
            } else if ($session->isLoggedIn()) {
                $response['status'] = 'SUCCESS';
                //$response['redirect'] = Mage::getUrl('customer/account/');
            } else if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    $response['status'] = 'SUCCESS';
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            $response['status'] = 'ERROR';
                            $response['message'] = $message;
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $response['status'] = 'ERROR';
                            $response['message'] = $e->getMessage();
                            break;
                        default:
                            $response['status'] = 'ERROR';
                            $response['message'] = $e->getMessage();
                    }
                    $session->addError($response['message']);
                    $session->setUsername($login['username']);
                }
            } else {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Login and password are required.');
            }

            if (!empty($response)) {
                if ($response['status'] == 'ERROR') {
                    if (isset($response['message'])) {
                        $session->addError($response['message']);
                    }
                    $response['html'] = $this->_getLayout('apdc_login_view');
                } else {
                    if (!isset($response['redirect'])) {
                        $response['redirect'] = $this->_loginPostRedirect();
                    }
                }
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }
        }

        return;
    }

    public function ajaxForgotPasswordAction()
    {
        $session = $this->_getSession();

        $params = $this->getRequest()->getPost();
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $response = array();
        if ($params['isAjax'] == 1) {
            $email = $params['email'];
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                    $response['status'] = 'SUCCESS';
                    $this->_getSession()
                    ->addSuccess($this->_getHelper('customer')
                    ->__('Nous venons d\envoyer un mail à l\'adresse %s pour que vous puissiez réinitialiser votre mot de passe..',
                        $this->_getHelper('customer')->escapeHtml($email)));
                } catch (Exception $exception) {
                    $response['status'] = 'ERROR';
                    $this->_getSession()->addError($exception->getMessage());
                }
            } else {
                $response['status'] = 'ERROR';
                $this->_getSession()->addError('Nous ne trouvons pas votre compte. Merci de bien vouloir vérifier votre email.');
            }
            if (!empty($response)) {
                $response['html'] = $this->_getLayout('apdc_forgotpassword_view');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }

            return;
        }
    }

    public function ajaxRegisterProcessAction()
    {
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {

            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $session = $this->_getSession();
            $response = array();

            if (!$this->_validateFormKey()) {
                $response['status'] = 'ERROR';
                $response['message'] = Mage::helper('customer')->__('Invalid form key');
            } else if ($session->isLoggedIn()) {
                $response['status'] = 'SUCCESS';
                //$response['redirect'] = Mage::getUrl('customer/account/');
            } else if (!$this->getRequest()->isPost()) {
                $response['status'] = 'ERROR';
                $response['message'] = Mage::helper('customer')->__('Missing post data');
            } else {

                $customer = $this->_getCustomer();

                try {
                    $errors = $this->_getCustomerErrors($customer);

                    if (empty($errors)) {
                        $customer->cleanPasswordsValidationData();
                        $customer->save();
                        $this->_dispatchRegisterSuccess($customer);
                        $response['redirect'] = $this->_successProcessRegistration($customer);
                        if ($params['customer_neighborhood']) {
                            $neighborhood = Mage::getModel('apdc_neighborhood/neighborhood')->load((int)$params['customer_neighborhood']);
                            if ($neighborhood && $neighborhood->getId()) {
                                $response['redirect'] = $neighborhood->getStoreUrl();
                            }
                        }
                        Mage::getSingleton('core/session')->addSuccess('Votre compte a bien été créé. Bienvenue chez Au Pas De Courses !');
                        $response['status'] = 'SUCCESS';
                    } else {
                        $response['status'] = 'ERROR';
                        $response['message'] = implode("\n", $errors);
                    }
                } catch (Mage_Core_Exception $e) {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                        $url = Mage::getUrl('apdc-customer/account/ajaxPopupView');
                        $message = $this->__('Il y a déjà un compte avec cette adresse email. Si vous êtes sûr qu\'il s\'agit de votre adresse email, <a href="#" data-login-view="%s" id="forgot-password">cliquez ici</a> pour obtenir votre mot de passe et accéder à votre compte.', $url);
                    } else {
                        $message = $this->_escapeHtml($e->getMessage());
                    }
                    $response['status'] = 'ERROR';
                    $response['message'] = $message;
                } catch (Exception $e) {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    $session->addException($e, $this->__('Cannot save the customer.'));
                    $response['status'] = 'ERROR';
                }
            }

            if (!empty($response)) {
                if ($response['status'] == 'ERROR') {
                    if (isset($errors) && !empty($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($this->_escapeHtml($errorMessage));
                        }
                    } else if (isset($response['message'])) {
                        $session->addError($response['message']);
                    }
                    $response['html'] = $this->_getLayout('apdc_register_view');
                }
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }
        }

        return;
    }

    /**
     * Dispatch Event
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _dispatchRegisterSuccess($customer)
    {
        Mage::dispatchEvent('customer_register_success',
            array('account_controller' => $this, 'customer' => $customer)
        );
    }

    /**
     * Success Registration
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_AccountController
     */
    protected function _successProcessRegistration(Mage_Customer_Model_Customer $customer)
    {
        $session = $this->_getSession();
        if ($customer->isConfirmationRequired()) {
            /** @var $store  Mage_Core_Model_Store*/
            $store = Mage::app()->getStore();
            $customer->sendNewAccountEmail(
                'confirmation',
                $session->getBeforeAuthUrl(),
                $store->getId()
            );
            $customerHelper = $this->_getHelper('customer');
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
                $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
            $url = $this->_getUrl('customer/account/index', array('_secure' => true));
        } else {
            $session->setCustomerAsLoggedIn($customer);
            $url = $this->_welcomeCustomer($customer);
        }
        return $this->getSuccessUrl($url);
    }

    /**
     * get success url
     *
     * @param string $defaultUrl
     * @return Mage_Core_Controller_Varien_Action
     */
    protected function getSuccessUrl($defaultUrl)
    {
        $successUrl = $this->getRequest()->getParam(self::PARAM_NAME_SUCCESS_URL);
        if (empty($successUrl)) {
            $successUrl = $defaultUrl;
        }
        if (!$this->_isUrlInternal($successUrl)) {
            $successUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $successUrl;
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess(
            $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );
        if ($this->_getHelper('customer/address')->isVatValidationEnabled(null)) {
            // Show corresponding VAT message to customer
            $configAddressType =  $this->_getHelper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = $this->_getUrl('*/*/index', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Get Customer Model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        $customer = Mage::registry('current_customer');
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        return $customer;
    }

    /**
     * Validate customer data and return errors if they are
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array|string
     */
    protected function _getCustomerErrors($customer)
    {
        $errors = array();
        $request = $this->getRequest();

        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);
        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($customerData);
            $customer->setPassword($request->getPost('password'));
            $customer->setPasswordConfirmation($request->getPost('confirmation'));
            $customerErrors = $customer->validate();
            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }
        return $errors;
    }


    /**
     * Get Customer Form Initalized Model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerForm($customer)
    {
        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = $this->_getModel('customer/form');
        $customerForm->setFormCode('customer_account_create');
        $customerForm->setEntity($customer);
        return $customerForm;
    }

    /**
     * Escape message text HTML.
     *
     * @param string $text
     * @return string
     */
    protected function _escapeHtml($text)
    {
        return Mage::helper('core')->escapeHtml($text);
    }

    /**
     * Get Url method
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }
/**
     * Customer logout action
     */
    public function logoutAction()
    {
        $session = $this->_getSession();
        $session->logout()->renewSession();

        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }
		Mage::getSingleton('customer/session')->addSuccess('Vous avez bien été déconnecté');
        return $this->getResponse()->setRedirect(Mage::getBaseUrl());
		//$this->_redirect(Mage::getBaseUrl());
    }

}
