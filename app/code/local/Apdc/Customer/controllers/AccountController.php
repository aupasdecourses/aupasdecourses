<?php

class Apdc_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
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
     * @param string     $path
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
            if (!empty($login['username']) && !empty($login['password'])) {
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
                $session->addError($this->__('Login and password are required.'));
            }

            if (!empty($response)) {
                if ($response['status'] == 'ERROR') {
                    $response['html'] = $this->_getLayout('apdc_login_view');
                } else {
                    $response['redirect'] = $this->_loginPostRedirect();
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
}
