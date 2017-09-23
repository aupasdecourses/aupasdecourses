<?php
/**
 * Copyright (c) 2015, Marcel Hauri
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright 2015, Marcel Hauri (https://github.com/mhauri/magento-slack/)
 *
 * @category Notification
 * @package mhauri-slack
 * @author Marcel Hauri <marcel@hauri.me>
 */

abstract class Intelivemetrics_Slack_Model_Abstract extends Mage_Core_Model_Abstract
{

    const LOG_FILE                      = 'slack.log';

    const DEFAULT_SENDER                = 'Magento Slack';
    const DEFAULT_CHANNEL               = '#general';
    const DEFAULT_ICON                  = ':bell:';

    const ENABLE_NOTIFICATION_PATH      = 'slack/general/enable_notification';
    const ENABLE_LOG_PATH               = 'slack/general/enable_log';
    const USE_QUEUE                     = 'slack/general/use_queue';

    const WEBHOOK_URL_PATH              = 'slack/api/webhook_url';
    const CHANNEL_PATH                  = 'slack/api/channel';
    const USERNAME_PATH                 = 'slack/api/username';
    const ICON_PATH                     = 'slack/api/icon';

    const NEW_ORDER_PATH                = 'slack/notification/new_order';
    const NEW_INVOICE_PATH                = 'slack/notification/new_invoice';
    const DAILY_STATS_PATH                = 'slack/notification/daily_stats';
    const NEW_CREDITMEMO_PATH                = 'slack/notification/new_creditmemo';
    const NEW_SHIPMENT_PATH                = 'slack/notification/new_shipment';
    const NEW_CUSTOMER_ACCOUNT_PATH     = 'slack/notification/new_customer_account';
    const ADMIN_USER_LOGIN_FAILED_PATH  = 'slack/notification/admin_user_login_failed';
    const ADMIN_USER_LOGIN_SUCCESS_PATH  = 'slack/notification/admin_user_login_success';

    /**
     * Store the Message
     * @var string
     */
    private $_message       = '';

    /**
     * Store the from name
     * @var string
     */
    private $_channel       = null;

    /**
     * @var null
     */
    private $_icon          = self::DEFAULT_ICON;

    /**
     * Store room id
     * @var null
     */
    private $_username      = null;

    /**
     * Store webhook url
     * @var null
     */
    private $_webhook        = null;
    
    /**
     * Message author/ Store name
     * @var null
     */
    private $_author = null;
    
    /**
     * Store link
     * @var null
     */
    private $_author_link = null;
    
    /**
     * Store favicon
     * @var null
     */
    private $_author_icon = null;
    
    private $_pretext = null;
    


    protected function _construct()
    {
        $this->setWebhookUrl(Mage::getStoreConfig(self::WEBHOOK_URL_PATH, 0));
        $this->setUsername(Mage::getStoreConfig(self::USERNAME_PATH, 0));
        $this->setChannel(Mage::getStoreConfig(self::CHANNEL_PATH, 0));
        $this->setIcon(Mage::getStoreConfig(self::ICON_PATH, 0));
        $this->setAuthor(Mage::getStoreConfig('general/store_information/name', 0));
        $this->setAuthorLink(Mage::getStoreConfig('web/secure/base_url', 0));
        $this->setAuthorIcon(Mage::getStoreConfig('web/secure/base_url', 0).'media/favicon/'.Mage::getStoreConfig('slack/api/icon', 0));
        $this->setWebhookUrl(Mage::getStoreConfig(self::WEBHOOK_URL_PATH, 0));
        parent::_construct();
    }

    /**
     * @param $webhook
     * @return $this
     */
    public function setWebhookUrl($webhook)
    {
        if(is_string($webhook)) {
            $this->_webhook = $webhook;
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getWebhookUrl()
    {
        return $this->_webhook;
    }

    /**
     * @param $channel
     * @return $this
     */
    public function setChannel($channel)
    {
        if(is_string($channel)) {
            $this->_channel = $channel;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        if($this->_channel) {
            return $this->_channel;
        }

        return self::DEFAULT_CHANNEL;
    }

    /**
     * @param $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        if(is_string($icon)) {
            $this->_icon = $icon;
        }
        return $this;
    }

    /**
     * @return null
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        if(is_string($username)) {
            $this->_username = $username;
        }

        return $this;
    }
    
    public function setPretext($pretext)
    {
        if(is_string($pretext)) {
            $this->_pretext = $pretext;
        }

        return $this;
    }
    
    public function setAuthor($author)
    {
        if(empty($author)){
            $author  = 'Magento Store';
        }
        if(is_string($author)) {
            $this->_author = $author;
        }

        return $this;
    }
    
    public function setAuthorLink($link)
    {
        if(is_string($link)) {
            $this->_author_link = $link;
        }

        return $this;
    }
    
    public function setAuthorIcon($icon)
    {
        if(is_string($icon)) {
            $this->_author_icon = $icon;
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getUsername()
    {
        return $this->_username;
    }
    
    public function getAuthor()
    {
        return $this->_author;
    }
    
    public function getPretext()
    {
        return $this->_pretext;
    }
    
    public function getAuthorLink()
    {
        return $this->_author_link;
    }
    
    public function getAuthorIcon()
    {
        return $this->_author_icon;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        if(is_string($message)) {
            $this->_message = $message;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig(self::ENABLE_NOTIFICATION_PATH, 0);
    }

    /**
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function sendMessage($params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->getWebhookUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('payload' => json_encode($params)));

        if(curl_exec($ch)) {
            if(Mage::getStoreConfig(self::ENABLE_LOG_PATH, 0)) {
                Mage::log('Message sent: ' . $this->getMessage(), Zend_Log::INFO, self::LOG_FILE, true);
            }
//        print_r(json_encode($params));
//        die();
        } else {
            throw new Exception('Unable to send Message');
        }
        curl_close($ch);
        return true;
    }
}